<?php

namespace Xsanisty\UserManager\Controller;

use Exception;
use Intervention\Image\ImageManager;
use Xsanisty\UserManager\Contract\UserRepositoryInterface;
use Xsanisty\UserManager\Contract\GroupRepositoryInterface;
use Xsanisty\UserManager\Contract\PermissionRepositoryInterface;
use Xsanisty\Admin\DashboardModule;
use YoHang88\LetterAvatar\LetterAvatar;

class UserController
{
    protected $image;
    protected $userRepository;
    protected $groupRepository;
    protected $permissionRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        GroupRepositoryInterface $groupRepository,
        PermissionRepositoryInterface $permissionRepository,
        ImageManager $image
    ) {
        $this->image = $image;
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
        $this->permissionRepository = $permissionRepository;
    }


    /**
     * Display list of all available users.
     *
     * @return Response
     */
    public function index()
    {
        return Response::view(
            '@silexstarter-usermanager/user/index',
            [
                'title'             => 'Manage Users',
                'page_title'        => 'Manage Users',
                'page_description'  => 'modify user\'s data, group, permission, etc',
                'permissions'       => $this->permissionRepository->groupByCategory(),
                'groups'            => $this->groupRepository->findAll(),
                'user_form_template'=> Config::get('@silexstarter-usermanager.config.user_form_template'),
                'active_menu'       => 'admin_sidebar.user-manager.manage-user'
            ]
        );
    }

    /**
     * Build a datatable response
     * @return Response
     */
    public function datatable()
    {
        $currentUser    = $this->userRepository->getCurrentUser();
        $hasEditAccess  = $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.user.edit']) : false;
        $hasDeleteAccess= $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.user.delete']) : false;
        $editTemplate   = '<button href="%s" class="btn btn-xs btn-primary btn-edit" style="margin-right: 5px">edit</button>';
        $deleteTemplate = '<button href="%s" class="btn btn-xs btn-danger btn-delete" style="margin-right: 5px">delete</button>';

        $datatable      = Datatable::of($this->userRepository->createDatatableQuery())
                        ->setColumn(['profile_pic', 'first_name', 'last_name', 'email', 'activated', 'last_login', 'id'])
                        ->setFormatter(
                            function ($row) use ($hasEditAccess, $hasDeleteAccess, $editTemplate, $deleteTemplate) {
                                $editButton     = $hasEditAccess
                                                ? sprintf($editTemplate, Url::to('usermanager.user.edit', ['id' => $row->id]))
                                                : '';
                                $deleteButton   = $hasDeleteAccess
                                                ? sprintf($deleteTemplate, Url::to('usermanager.user.delete', ['id' => $row->id]))
                                                : '';

                                $profilePic     = !$row->profile_pic // TODO update if LetterAvatar has new release
                                                ? '<img src="'. (new LetterAvatar("$row->first_name $row->last_name")) .'" class="img-circle img-sm" style="margin:0 10px" />'
                                                : '<img src="'. Asset::resolvePath('img/profile/' . $row->profile_pic) .'" class="img-circle img-sm" />';

                                return [
                                    $profilePic,
                                    $row->first_name,
                                    $row->last_name,
                                    $row->email,
                                    $row->activated == 1 ? '<span class="label label-success">active</span>' : '<span class="label label-danger">suspended</span>',
                                    $row->last_login ? $row->last_login->format('Y-m-d') : '',
                                    $editButton.$deleteButton
                                ];
                            }
                        )
                        ->make();

        return Response::json($datatable);
    }

    /**
     * Display user create form for non ajax request.
     *
     * @return Response
     */
    public function create()
    {
        return Response::view(
            '@silexstarter-usermanager/user/edit',
            [
                'permissions'       => $this->permissionRepository->findAll(),
                'groups'            => $this->groupRepository->findAll(),
                'user_form_template'=> Config::get('@silexstarter-usermanager.config.user_form_template')
            ]
        );
    }

    /**
     * Store new created user object.
     *
     * @return Response
     */
    public function store()
    {
        try {
            $data       = Request::except(['_method', 'id']);
            $picture    = Request::file('profile_pic');

            if ($picture && $picture->isValid()) {
                $newName    = md5(microtime()) . '.' . $picture->guessClientExtension();
                $targetDir  = Config::get('app')['path.public'] . 'assets/img/profile/';

                $this->image->make($picture)->fit(250)->save($targetDir . $newName);

                $data['profile_pic'] = $newName;
            }

            $this->userRepository->create($data);

            return Response::ajax('User has been created', 201);
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while creating user',
                500,
                [[
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]]
            );
        }
    }

    /**
     * Display form to edit user.
     *
     * @param  int $id  The user's id
     *
     * @return Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->findById($id);
        
        // TODO update if LetterAvatar has new release
        $user->profile_pic = $user->profile_pic ? 
            Asset::resolvePath('img/profile/' . $user->profile_pic) : (new LetterAvatar("$user->first_name $user->last_name", 'circle', 128))->__toString();
        
        if (Request::ajax()) {
            $userArray      = $user->toArray();
            $user['groups'] = $user->getGroups();

            return Response::json($user->toArray());
        }

        return View::make(
            '@silexstarter-usermanager/user/edit',
            [
                'user' => $user,
                'groups' => $this->groupRepository->findAll(),
                'permissions' => $this->permissionRepository->findAll(),
                'user_form_template' => Config::get('@silexstarter-usermanager.config.user_form_template')
            ]
        );
    }

    public function update($id)
    {
        try {
            $data   = Request::except(['_method', 'id']);
            $picture= Request::file('profile_pic');
            $user   = $this->userRepository->findById($id);

            if ($picture && $picture->isValid()) {
                $newName    = md5(microtime()) . '.' . $picture->guessClientExtension();
                $targetDir  = Config::get('app')['path.public'] . 'assets/img/profile/';

                $this->image->make($picture)->fit(250)->save($targetDir . $newName);

                $data['profile_pic'] = $newName;

                if ($user->profile_pic) {
                    File::remove($targetDir . $user->profile_pic);
                }
            }

            $this->userRepository->update($id, $data);

            return Response::ajax('User has been updated');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while updating user',
                500,
                [[
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]]
            );
        }
    }

    public function delete($id)
    {
        try {
            $user    = $this->userRepository->findById($id);

            if ($user->profile_pic) {
                $targetDir  = Config::get('app')['path.public'] . 'assets/img/profile/';
                File::remove($targetDir . $user->profile_pic);
            }

            $success = $this->userRepository->delete($id);

            return Response::ajax('User has been deleted');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while deleting user',
                500,
                [[
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]]
            );
        }
    }

    public function profilePicture($id)
    {
        $user = $this->userRepository->findById($id);
        $dir  = Config::get('app')['path.public'] . 'assets/img/profile/';

        return Response::file($dir . $user->profile_pic);
    }
}

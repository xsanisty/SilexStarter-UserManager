<?php

namespace Xsanisty\UserManager\Controller;

use Exception;
use Xsanisty\UserManager\Repository\GroupRepositoryInterface;
use Xsanisty\UserManager\Repository\UserRepositoryInterface;
use Xsanisty\UserManager\Repository\PermissionRepositoryInterface;
use Xsanisty\Admin\DashboardModule;

class UserController
{
    protected $userRepository;
    protected $groupRepository;
    protected $permissionRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        GroupRepositoryInterface $groupRepository,
        PermissionRepositoryInterface $permissionRepository
    ) {
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
        Event::fire(DashboardModule::INIT);
        Menu::get('admin_sidebar')->setActive('user-manager.manage-user');

        return Response::view(
            '@silexstarter-usermanager/user/index',
            [
                'title'             => 'Manage Users',
                'page_title'        => 'Manage Users',
                'permissions'       => $this->permissionRepository->groupByCategory(),
                'groups'            => $this->groupRepository->findAll(),
                'current_user'      => $this->userRepository->getCurrentUser(),
                'user_form_template'=> Config::get('@silexstarter-usermanager.config.user_form_template')
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
                        ->setColumn(['first_name', 'last_name', 'email', 'activated', 'last_login', 'id'])
                        ->setFormatter(
                            function ($row) use ($hasEditAccess, $hasDeleteAccess, $editTemplate, $deleteTemplate) {
                                $editButton     = $hasEditAccess
                                                ? sprintf($editTemplate, Url::to('usermanager.user.edit', ['id' => $row->id]))
                                                : '';
                                $deleteButton   = $hasDeleteAccess
                                                ? sprintf($deleteTemplate, Url::to('usermanager.user.delete', ['id' => $row->id]))
                                                : '';

                                return [
                                    $row->first_name,
                                    $row->last_name,
                                    $row->email,
                                    $row->activated == 1 ? '<span class="label label-success">active</span>' : '<span class="label label-danger">suspended</span>',
                                    $row->last_login ? $row->last_login->format('Y-m-d H:i') : '',
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
            $user = Request::except(['_method', 'id']);

            $this->userRepository->create($user);

            return Response::ajax('User has been created');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while creating user',
                500,
                [
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]
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
            $userData = Request::get();

            unset($userData['_method']);
            unset($userData['id']);

            $this->userRepository->update($id, $userData);

            return Response::ajax('User has been updated');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while updating user',
                500,
                [
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]
            );
        }
    }

    public function delete($id)
    {
        try {
            $success = $this->userRepository->delete($id);

            return Response::ajax('User has been deleted');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while deleting user',
                500,
                [
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]
            );
        }
    }
}

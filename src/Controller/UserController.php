<?php

namespace Xsanisty\UserManager\Controller;

use Exception;
use SilexStarter\Response\AjaxStatusResponse;
use Xsanisty\UserManager\Repository\UserRepository;
use Xsanisty\UserManager\Repository\GroupRepository;
use Xsanisty\UserManager\Repository\PermissionRepository;
use Xsanisty\Admin\DashboardModule;
use SilexStarter\Controller\DispatcherAwareController;

class UserController extends DispatcherAwareController
{
    protected $userRepository;
    protected $groupRepository;
    protected $permissionRepository;

    public function __construct(
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        PermissionRepository $permissionRepository
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
        $this->getDispatcher()->dispatch(DashboardModule::INIT);
        Menu::get('admin_sidebar')->setActive('user-manager.manage-user');

        return Response::view(
            '@silexstarter-usermanager/user/index',
            [
                'title' => 'Manage Users',
                'permissions' => $this->permissionRepository->groupByCategory(),
                'groups' => $this->groupRepository->findAll(),
                'user'  => $this->userRepository->getCurrentUser()
            ]
        );
    }

    /**
     * Build a datatable response
     * @return Response
     */
    public function datatable()
    {
        return Response::json($this->userRepository->createDatatableResponse());
    }

    public function create()
    {
        return Response::view(
            '@silexstarter-usermanager/user/edit',
            [
                'permissions' => $this->permissionRepository->findAll(),
                'groups' => $this->groupRepository->findAll()
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
            $user = Request::get();
            unset($user['_method']);

            $this->userRepository->create($user);

            return Response::ajax('User has been created');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while creating user',
                500,
                false,
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
                'permissions' => $this->permissionRepository->findAll()
            ]
        );
    }

    public function update($id)
    {

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
                false,
                [[
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]]
            );
        }
    }
}

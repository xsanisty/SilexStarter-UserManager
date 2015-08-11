<?php

namespace Xsanisty\UserManager\Controller;

use Exception;
use Xsanisty\Admin\DashboardModule;
use SilexStarter\Controller\DispatcherAwareController;
use Xsanisty\UserManager\Repository\PermissionRepository;
use Xsanisty\UserManager\Repository\UserRepository;

class PermissionController extends DispatcherAwareController
{
    protected $userRepo;
    protected $permissionRepo;

    public function __construct(PermissionRepository $permissionRepo, UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
        $this->permissionRepo = $permissionRepo;
    }

    public function index()
    {
        $this->getDispatcher()->dispatch(DashboardModule::INIT);
        Menu::get('admin_sidebar')->setActive('user-manager.manage-permission');

        return Response::view(
            '@silexstarter-usermanager/permission/index',
            [
                'title'     => 'Manage User Permissions',
                'user'      => $this->userRepo->getCurrentUser(),
                'page_title'=> 'Manage User Permissions'
            ]
        );
    }

    public function datatable()
    {
        return Response::json($this->permissionRepo->createDatatableResponse());
    }

    public function store()
    {
        try {
            $this->permissionRepo->create(
                [
                    'name'          => Request::get('name'),
                    'category'      => Request::get('category'),
                    'description'   => Request::get('description')
                ]
            );

            return Response::ajax('New permission has been created');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while creating new permission',
                500,
                false,
                [
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]
            );
        }

    }

    public function edit($id)
    {
        if (Request::ajax()) {
            return Response::json($this->permissionRepo->findById($id));
        }
    }

    public function update($id)
    {
        try {
            $this->permissionRepo->update(
                Request::get('id'),
                [
                    'name'          => Request::get('name'),
                    'category'      => Request::get('category'),
                    'description'   => Request::get('description')
                ]
            );

            return Response::ajax('Permission updated');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while updating permission',
                500,
                false,
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
            $this->permissionRepo->delete($id);

            return Response::ajax('Permission deleted');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while deleting permission',
                500,
                false,
                [
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]
            );
        }
    }
}

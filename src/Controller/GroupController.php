<?php

namespace Xsanisty\UserManager\Controller;

use Xsanisty\Admin\DashboardModule;
use SilexStarter\Controller\DispatcherAwareController;
use Xsanisty\UserManager\Repository\GroupRepository;
use Xsanisty\UserManager\Repository\UserRepository;
use Xsanisty\UserManager\Repository\PermissionRepository;

class GroupController extends DispatcherAwareController
{
    protected $groupRepository;
    protected $userRepository;
    protected $permissionRepository;

    public function __construct(GroupRepository $groupRepository, UserRepository $userRepository, PermissionRepository $permissionRepository)
    {
        $this->groupRepository        = $groupRepository;
        $this->userRepository         = $userRepository;
        $this->permissionRepository   = $permissionRepository;
    }

    public function index()
    {
        $this->getDispatcher()->dispatch(DashboardModule::INIT);
        Menu::get('admin_sidebar')->setActive('user-manager.manage-group');

        return View::make(
            '@silexstarter-usermanager/group/index',
            [
                'title'         => 'Manage User Groups',
                'user'          => $this->userRepository->getCurrentUser(),
                'permissions'   => $this->permissionRepository->groupByCategory()
            ]
        );
    }

    public function datatable()
    {
        return Response::json($this->groupRepository->createDatatableResponse());
    }

    public function store()
    {
        try {
            $group = Request::get();
            unset($group['_method']);
            unset($group['id']);

            $this->groupRepository->create($group);

            return Response::ajax('Group has been created');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while creating group',
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
            return Response::json($this->groupRepository->findById($id));
        }
    }

    public function update($id)
    {
        try {
            $group = Request::get();
            unset($group['_method']);
            unset($group['id']);

            $this->groupRepository->update($id, $group);

            return Response::ajax('Group has been updated');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while updating group',
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

            $this->groupRepository->delete($id);

            return Response::ajax('Group has been deleted');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while deleting group',
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

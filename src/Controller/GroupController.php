<?php

namespace Xsanisty\UserManager\Controller;

use Exception;
use Xsanisty\Admin\DashboardModule;
use Cartalyst\Sentry\Users\UserInterface;
use Xsanisty\UserManager\Contract\GroupRepositoryInterface;
use Xsanisty\UserManager\Contract\PermissionRepositoryInterface;

class GroupController
{
    protected $groupRepository;
    protected $user;
    protected $permissionRepository;

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        UserInterface $user,
        PermissionRepositoryInterface $permissionRepository
    ) {
        $this->groupRepository      = $groupRepository;
        $this->user                 = $user;
        $this->permissionRepository = $permissionRepository;
    }

    public function index()
    {
        Event::fire(DashboardModule::INIT);
        Menu::get('admin_sidebar')->setActive('user-manager.manage-group');
        Menu::get('admin_breadcrumb')->createItem(
            'manage-group',
            [
                'label' => 'Manage Groups',
                'icon'  => 'users',
                'url'   => Url::to('usermanager.group.index')
            ]
        );

        return View::make(
            '@silexstarter-usermanager/group/index',
            [
                'title'         => 'Manage Groups',
                'page_title'    => 'Manage Groups',
                'permissions'   => $this->permissionRepository->groupByCategory()
            ]
        );
    }

    public function datatable()
    {
        $currentUser    = $this->user;
        $hasEditAccess  = $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.group.edit']) : false;
        $hasDeleteAccess= $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.group.delete']) : false;
        $editTemplate   = '<button href="%s" class="btn btn-xs btn-primary btn-edit" style="margin-right: 5px">edit</button>';
        $deleteTemplate = '<button href="%s" class="btn btn-xs btn-danger btn-delete" style="margin-right: 5px">delete</button>';

        $datatable      = Datatable::of($this->groupRepository->createDatatableQuery())
                        ->setColumn(['name', 'description', 'id'])
                        ->setFormatter(
                            function ($row) use ($hasEditAccess, $hasDeleteAccess, $editTemplate, $deleteTemplate) {
                                $editButton     = $hasEditAccess
                                                ? sprintf($editTemplate, Url::to('usermanager.group.edit', ['id' => $row->id]))
                                                : '';
                                $deleteButton   = $hasDeleteAccess
                                                ? sprintf($deleteTemplate, Url::to('usermanager.group.delete', ['id' => $row->id]))
                                                : '';
                                return [
                                    $row->name,
                                    $row->description,
                                    $editButton.$deleteButton
                                ];
                            }
                        )
                        ->make();

        return Response::json($datatable);
    }

    public function store()
    {
        try {
            $group = Request::only(['name', 'description', 'permissions'], [null, '', []]);

            $this->groupRepository->create($group);

            return Response::ajax('Group has been created', 201);
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while creating group',
                500,
                [[
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]]
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
            $group = Request::only(['name', 'description', 'permissions'], [null, '', []]);

            $this->groupRepository->update($id, $group);

            return Response::ajax('Group has been updated');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while updating group',
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

            $this->groupRepository->delete($id);

            return Response::ajax('Group has been deleted');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while deleting group',
                500,
                [[
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]]
            );
        }
    }
}

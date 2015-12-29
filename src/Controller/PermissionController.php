<?php

namespace Xsanisty\UserManager\Controller;

use Exception;
use Xsanisty\Admin\DashboardModule;
use Cartalyst\Sentry\Users\UserInterface;
use Xsanisty\UserManager\Contract\PermissionRepositoryInterface;

class PermissionController
{
    protected $user;
    protected $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository, UserInterface $user)
    {
        $this->user = $user;
        $this->permissionRepository = $permissionRepository;
    }

    public function index()
    {
        Event::fire(DashboardModule::INIT);
        Menu::get('admin_sidebar')->setActive('user-manager.manage-permission');

        Menu::get('admin_breadcrumb')->createItem(
            'manage-permission',
            [
                'label' => 'Manage Permissions',
                'icon'  => 'th-list',
                'url'   => Url::to('usermanager.permission.index')
            ]
        );

        return Response::view(
            '@silexstarter-usermanager/permission/index',
            [
                'title'     => 'Manage User Permissions',
                'page_title'=> 'Manage User Permissions'
            ]
        );
    }

    public function datatable()
    {
        $currentUser    = $this->user;
        $hasEditAccess  = $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.permission.edit']) : false;
        $hasDeleteAccess= $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.permission.delete']) : false;
        $editTemplate   = '<button href="%s" class="btn btn-xs btn-primary btn-edit" style="margin-right: 5px">edit</button>';
        $deleteTemplate = '<button href="%s" class="btn btn-xs btn-danger btn-delete" style="margin-right: 5px">delete</button>';

        $datatable      = Datatable::of($this->permissionRepository->createDatatableQuery())
                        ->setColumn(['name', 'category', 'description', 'id'])
                        ->setFormatter(
                            function ($row) use ($hasEditAccess, $hasDeleteAccess, $editTemplate, $deleteTemplate) {
                                $editButton     = $hasEditAccess
                                                ? sprintf($editTemplate, Url::to('usermanager.permission.edit', ['id' => $row->id]))
                                                : '';
                                $deleteButton   = $hasDeleteAccess
                                                ? sprintf($deleteTemplate, Url::to('usermanager.permission.delete', ['id' => $row->id]))
                                                : '';
                                return [
                                    $row->name,
                                    $row->category,
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
            $this->permissionRepository->create(
                [
                    'name'          => Request::get('name'),
                    'category'      => Request::get('category'),
                    'description'   => Request::get('description')
                ]
            );

            return Response::ajax('New permission has been created', 201);
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while creating new permission',
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
            return Response::json($this->permissionRepository->findById($id));
        }
    }

    public function update($id)
    {
        try {
            $this->permissionRepository->update(
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
            $this->permissionRepository->delete($id);

            return Response::ajax('Permission deleted');
        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while deleting permission',
                500,
                [[
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]]
            );
        }
    }
}

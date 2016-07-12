<?php

namespace Xsanisty\UserManager\Controller;

use Exception;
use Xsanisty\UserManager\Contract\CompanyUserRepositoryInterface;
use Xsanisty\Admin\DashboardModule;
use Cartalyst\Sentry\Users\UserInterface;

class CompanyUserController
{
    protected $user;
    protected $repository;

    /**
     * Constructing the controller.
     *
     * @param CompanyUserRepositoryInterface $repository  The repository object
     */
    public function __construct(CompanyUserRepositoryInterface $repository, UserInterface $user)
    {
        $this->user = $user;
        $this->repository = $repository;
    }

    /**
     * Show all available resources.
     */
    public function index()
    {
        Event::fire(DashboardModule::INIT);
        Menu::get('admin_breadcrumb')->createItem(
            'manage-company_user',
            [
                'label' => 'My Company',
                'url'   => Url::to('silexstarter-usermanager.company_user.index')
            ]
        );

        return View::make(
            '@silexstarter-usermanager/company_user/index',
            [
                'title'     => 'My Company',
                'page_title'=> 'My Company',
            ]
        );
    }

    /**
     * Show single resource with specific id.
     */
    public function show($id)
    {
        $data = $this->repository->findById($id);

        if ($data) {
            return Response::json($data);
        }

        return Response::ajax(
            ucfirst('company_user') . ' not found!',
            404,
            [[
                'message'   => $e->getMessage(),
                'code'      => $e->getCode()
            ]]
        );
    }

    /**
     * Create new resource in database.
     */
    public function store()
    {
        $data   = Request::get('data');

        try {
            $entity = $this->repository->create($data);

            return Response::ajax('New ' . ucfirst('company_user') . ' has been created!', 201);

        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while creating new company_user!',
                500,
                [[
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]]
            );
        }
    }

    /**
     * Update existing resource in the database.
     */
    public function update($id)
    {
        $data = Request::get('data');

        try {
            $entity = $this->repository->update($id, $data);

            return Response::ajax(ucfirst('company_user') . ' has been updated!');

        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while updating company_user!',
                500,
                [[
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]]
            );
        }
    }

    /**
     * Remove specified resource in database.
     */
    public function delete($id)
    {
        try {
            $this->repository->delete($id);

            return Response::ajax(ucfirst('company_user') . ' has been deleted!');

        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while deleting company_user!',
                500,
                [[
                    'message'   => $e->getMessage(),
                    'code'      => $e->getCode()
                ]]
            );
        }
    }

    /**
     * Build a datatable response.
     */
    public function datatable()
    {
        $datatable = Datatable::of($this->repository->createDatatableQuery($this->user))
        /** add more fields based on your table */
        ->setColumn(['company_id', 'primary', 'active', 'admin'])
        ->setFormatter(
            function ($row) {
                $primaryButton  = $row->primary
                                ? ''
                                : '<button href="' . Url::to('silexstarter-usermanager.company_user.show', ['id' => $row->company_id]) . '" class="btn btn-xs btn-primary btn-company_user-show" style="margin-right: 5px">set as primary</button>';
                $deleteButton   = $row->admin
                                ? '<button href="' . Url::to('silexstarter-usermanager.company_user.show', ['id' => $row->company_id]) . '" class="btn btn-xs btn-danger btn-company_user-edit" style="margin-right: 5px">remove</button>'
                                : '<button href="' . Url::to('silexstarter-usermanager.company_user.delete', ['id' => $row->company_id]) . '" class="btn btn-xs btn-warning btn-company_user-delete" style="margin-right: 5px">leave</button>';
                $logoImage      = $row->company->logo
                                ? '<img src="'. Asset::resolvePath('img/logo/' . $row->company->logo) .'" style="width: 100px" />'
                                : '';
                $adminBadge     = $row->admin
                                ? '<span class="label label-success" data-toggle="tooltip" title="You have admin priviledge on this company">admin</span>'
                                : '<span class="label label-primary" data-toggle="tooltip" title="You have user priviledge on this company">user</span>';
                $activeBadge    = $row->active
                                ? '<span class="label label-success" data-toggle="tooltip" title="This is your currently active company" >active</span> '
                                : '';
                $primaryBadge   = $row->primary
                                ? '<span class="label label-success" data-toggle="tooltip" title="This is your primary company" >primary</span> '
                                : '';

                /** format your fields as you need */
                return [
                    $logoImage,
                    $row->company->name,
                    $adminBadge,
                    $primaryBadge,
                    $activeBadge,
                    $primaryButton . $deleteButton
                ];
            }
        )
        ->make();

        return Response::json($datatable);
    }
}

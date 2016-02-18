<?php

namespace Xsanisty\UserManager\Controller;

use Exception;
use Xsanisty\Admin\DashboardModule;
use Intervention\Image\ImageManager;
use Xsanisty\UserManager\Contract\CompanyRepositoryInterface;

class CompanyController
{
    protected $image;
    protected $repository;
    protected $logoStorage;

    /**
     * Constructing the controller.
     *
     * @param CompanyRepositoryInterface $repository  The repository object
     */
    public function __construct(CompanyRepositoryInterface $repository, ImageManager $image)
    {
        $this->image = $image;
        $this->repository = $repository;
        $this->logoStorage = Config::get('app')['path.public'] . 'assets/img/logo/';
    }

    /**
     * Show all available resources.
     */
    public function index()
    {
        Event::fire(DashboardModule::INIT);
        Menu::get('admin_breadcrumb')->createItem(
            'manage-company',
            [
                'label' => ucfirst('company') . ' list',
                'url'   => Url::to('silexstarter-usermanager.company.index')
            ]
        );

        return View::make(
            '@silexstarter-usermanager/company/index',
            [
                'title'     => ucfirst('company') . ' list',
                'page_title'=> ucfirst('company') . ' list',
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
            ucfirst('company') . ' not found!',
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
        try {
            $data = Request::get('data');
            $logo = Request::file('logo_pic');

            if ($logo && $logo->isValid()) {
                $newName    = md5(microtime()) . '.' . $logo->guessClientExtension();
                $targetDir  = $this->logoStorage;

                $this->image->make($logo)->widen(250)->save($targetDir . $newName);

                $data['logo'] = $newName;
            }

            $entity = $this->repository->create($data);

            return Response::ajax('New ' . ucfirst('company') . ' has been created!', 201);

        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while creating new company!',
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

        try {
            $data   = Request::get('data');
            $logo   = Request::file('logo_pic');
            $company= $this->repository->findById($id);

            if ($logo && $logo->isValid()) {
                $newName    = md5(microtime()) . '.' . $logo->guessClientExtension();
                $targetDir  = $this->logoStorage;

                $this->image->make($logo)->widen(250)->save($targetDir . $newName);

                $data['logo'] = $newName;

                if ($company->logo) {
                    File::remove($targetDir . $company->logo);
                }
            }

            $entity = $this->repository->update($id, $data);

            return Response::ajax(ucfirst('company') . ' has been updated!');

        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while updating company!',
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
            $company = $this->repository->findById($id);

            if ($company->logo) {
                $targetDir  = $this->logoStorage;
                File::remove($targetDir . $company->logo);
            }

            $this->repository->delete($id);

            return Response::ajax(ucfirst('company') . ' has been deleted!');

        } catch (Exception $e) {
            return Response::ajax(
                'Error occured while deleting company!',
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
        $datatable = Datatable::of($this->repository->createDatatableQuery())
        /** add more fields based on your table */
        ->setColumn(['logo', 'name', 'address', 'country', 'phone', 'id'])
        ->setFormatter(
            function ($row) {
                $showButton   = '<button href="' . Url::to('usermanager.company.show', ['id' => $row->id]) . '" class="btn btn-xs btn-info btn-company-show" style="margin-right: 5px">show</button>';
                $editButton   = '<button href="' . Url::to('usermanager.company.show', ['id' => $row->id]) . '" class="btn btn-xs btn-primary btn-company-edit" style="margin-right: 5px">edit</button>';
                $deleteButton = '<button href="' . Url::to('usermanager.company.delete', ['id' => $row->id]) . '" class="btn btn-xs btn-danger btn-company-delete" style="margin-right: 5px">delete</button>';
                $logoImage    = '<img src="'. Asset::resolvePath('img/logo/' . $row->logo) .'" style="width: 100px" />';

                /** format your fields as you need */
                return [
                    //$row->id,
                    $row->logo ? $logoImage : '',
                    $row->name,
                    $row->address,
                    //$row->state,
                    $row->country,
                    $row->phone,
                    //$row->fax,

                    $showButton . $editButton . $deleteButton
                ];
            }
        )
        ->make();

        return Response::json($datatable);
    }
}

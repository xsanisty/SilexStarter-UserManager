<?php

namespace Xsanisty\UserManager\Repository;


use Xsanisty\UserManager\Model\Permission;
use Xsanisty\Datatable\DatatableResponseBuilder;

class PermissionRepository
{
    protected $permission;
    protected $datatable;
    protected $userRepo;

    public function __construct(Permission $permission, UserRepository $userRepo, DatatableResponseBuilder $datatable)
    {
        $this->permission = $permission;
        $this->datatable = $datatable;
        $this->userRepo = $userRepo;
    }

    public function groupByCategory()
    {
        return $this->permission->all()->groupBy('category');
    }

    public function findAll()
    {
        return $this->permission->all();
    }

    public function findByName($name)
    {
        return $this->permission->where('name', '=', $name)->first();
    }

    public function findById($id)
    {
        return $this->permission->find($id);
    }

    public function delete($id)
    {
        return $this->permission->where('id', '=', $id)->delete();
    }

    public function create($data)
    {
        return $this->permission->create($data);
    }

    public function update($id, array $data)
    {
        return $this->permission->where('id', '=', $id)->update($data);
    }

    public function createDatatableResponse()
    {
        $currentUser    = $this->userRepo->getCurrentUser();
        $hasEditAccess  = $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.permission.edit']) : false;
        $hasDeleteAccess= $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.permission.delete']) : false;

        return $this->datatable
                    ->of($this->permission)
                    ->setColumn(['name', 'category', 'description', 'id'])
                    ->setFormatter(
                        function ($row) use ($hasEditAccess, $hasDeleteAccess) {
                            $editButton     = $hasEditAccess
                                            ? '<button href="'.
                                                Url::to('usermanager.permission.edit', ['id' => $row->id]).
                                                '" class="btn btn-xs btn-primary btn-edit" style="margin-right: 5px">edit</button>'
                                            : '';
                            $deleteButton   = $hasDeleteAccess
                                            ? '<button href="'.
                                                Url::to('usermanager.permission.delete', ['id' => $row->id]).
                                                '" class="btn btn-xs btn-danger btn-delete" style="margin-right: 5px">delete</button>'
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
    }
}

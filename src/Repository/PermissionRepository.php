<?php

namespace Xsanisty\UserManager\Repository;


use Xsanisty\UserManager\Model\Permission;
use Xsanisty\Datatable\DatatableResponseBuilder;

class PermissionRepository implements PermissionRepositoryInterface
{
    protected $permission;
    protected $datatable;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function groupByCategory()
    {
        return $this->permission->orderBy('category')->get()->groupBy('category');
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

    public function createDatatableQuery()
    {
        return $this->permission->newQuery();
    }
}

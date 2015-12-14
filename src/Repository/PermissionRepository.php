<?php

namespace Xsanisty\UserManager\Repository;

use Xsanisty\UserManager\Model\Permission;
use Xsanisty\UserManager\Contract\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    protected $permission;
    protected $datatable;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * {@inheritdoc}
    */
    public function groupByCategory()
    {
        return $this->permission->orderBy('category')->get()->groupBy('category');
    }

    /**
     * {@inheritdoc}
    */
    public function findAll()
    {
        return $this->permission->all();
    }

    /**
     * {@inheritdoc}
    */
    public function findByName($name)
    {
        return $this->permission->where('name', '=', $name)->first();
    }

    /**
     * {@inheritdoc}
    */
    public function findById($id)
    {
        return $this->permission->find($id);
    }

    /**
     * {@inheritdoc}
    */
    public function delete($id)
    {
        return $this->permission->where('id', '=', $id)->delete();
    }

    /**
     * {@inheritdoc}
    */
    public function create($data)
    {
        return $this->permission->create($data);
    }

    /**
     * {@inheritdoc}
    */
    public function update($id, array $data)
    {
        return $this->permission->where('id', '=', $id)->update($data);
    }

    /**
     * {@inheritdoc}
    */
    public function createDatatableQuery()
    {
        return $this->permission->newQuery();
    }
}

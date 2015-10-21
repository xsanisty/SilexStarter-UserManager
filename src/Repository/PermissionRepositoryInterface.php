<?php

namespace Xsanisty\UserManager\Repository;

interface PermissionRepositoryInterface
{
    public function groupByCategory();

    public function findAll();

    public function findByName($name);

    public function findById($id);

    public function delete($id);

    public function create($data);

    public function update($id, array $data);

    public function createDatatableQuery();
}

<?php

namespace Xsanisty\UserManager\Contract;

interface GroupRepositoryInterface
{
    public function findById($id);

    public function findByName($name);

    public function findAll();

    public function create(array $groupData);

    public function update($id, $groupData);

    public function delete($id);

    public function createDatatableQuery();
}

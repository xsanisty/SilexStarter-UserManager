<?php
/**
 * Created by PhpStorm.
 * User: ikhsan
 * Date: 18/08/15
 * Time: 7:56
 */
namespace Xsanisty\UserManager\Repository;

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

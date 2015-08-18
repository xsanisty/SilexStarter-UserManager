<?php
/**
 * Created by PhpStorm.
 * User: ikhsan
 * Date: 18/08/15
 * Time: 7:59
 */
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

    public function createDatatableResponse();
}
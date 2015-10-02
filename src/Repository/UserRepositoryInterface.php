<?php
/**
 * Created by PhpStorm.
 * User: ikhsan
 * Date: 18/08/15
 * Time: 7:54
 */
namespace Xsanisty\UserManager\Repository;

interface UserRepositoryInterface
{
    public function findAll();

    public function findById($id);

    public function findByLogin($login);

    public function findByCredential(array $credential);

    public function delete($id);

    public function create(array $userData);

    public function update($userId, array $userData);

    public function getCurrentUser();

    public function createDatatableQuery();
}

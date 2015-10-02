<?php

namespace Xsanisty\UserManager\Repository;

use Exception;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\ProviderInterface;
use Xsanisty\Datatable\DatatableResponseBuilder;

class UserRepository implements UserRepositoryInterface
{
    protected $sentry;
    protected $userProvider;

    public function __construct(Sentry $sentry, ProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
        $this->sentry       = $sentry;
    }

    public function findAll()
    {
        return $this->userProvider->findAll();
    }

    public function findById($id)
    {
        return $this->userProvider->findById($id);
    }

    public function findByLogin($login)
    {
        return $this->userProvider->findByLogin($login);
    }

    public function findByCredential(array $credential)
    {
        return $this->userProvider->findByCredential($credential);
    }

    public function delete($id)
    {
        return  $this->findById($id)->delete();
    }

    public function create(array $userData)
    {
        $groups = [];

        if ($userData['password'] === $userData['confirm_password']) {
            unset($userData['confirm_password']);
        } else {
            throw new Exception("Password and confirmation password mismatch", 1);
        }

        if (isset($userData['groups'])) {
            $groups = $userData['groups'];
            unset($userData['groups']);
        }

        if (isset($userData['permissions'])) {
            $permissions = $userData['permissions'];
            $userData['permissions'] = [];

            foreach ($permissions as $perm) {
                $userData['permissions'][$perm] = 1;
            }
        }

        $user = $this->userProvider->create($userData);

        foreach ($groups as $groupId) {
            $group = $this->sentry->findGroupById($groupId);

            $user->addGroup($group);
        }
    }

    public function update($userId, array $userData)
    {
        $user   = $this->sentry->findUserById($userId);
        $groups = isset($userData['groups']) ? $userData['groups'] : [];

        if ($userData['password'] !== $userData['confirm_password']) {
            throw new Exception("Password and confirmation password mismatch", 1);
        }

        if (!$userData['password']) {
            unset($userData['password']);
        }

        unset($userData['groups'], $userData['confirm_password']);

        foreach ($user->getGroups() as $group) {
            if (!in_array($group->id, $groups)) {
                $user->removeGroup($group);
            } else {
                $key = array_search($group->id, $groups);

                unset($groups[$key]);
            }
        }

        $user->update($userData);

        foreach ($groups as $groupId) {
            $group = $this->sentry->findGroupById($groupId);

            $user->addGroup($group);
        }
    }

    public function getCurrentUser()
    {
        return $this->sentry->getUser();
    }

    public function createDatatableQuery()
    {
        $currentUser    = $this->getCurrentUser();

        return $this->userProvider->createModel()->where('id', '<>', $currentUser->id);
    }
}

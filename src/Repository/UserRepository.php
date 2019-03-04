<?php

namespace Xsanisty\UserManager\Repository;

use Exception;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\ProviderInterface;
use Xsanisty\UserManager\Contract\UserRepositoryInterface;
use Xsanisty\UserManager\Contract\PermissionRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    protected $sentry;
    protected $userProvider;
    protected $permissionRepo;

    public function __construct(
        Sentry $sentry,
        ProviderInterface $userProvider,
        PermissionRepositoryInterface $permissionRepo
    ) {
        $this->userProvider     = $userProvider;
        $this->sentry           = $sentry;
        $this->permissionRepo   = $permissionRepo;
    }

    /**
     * Get all available user.
     *
     */
    public function findAll()
    {
        return $this->userProvider->findAll();
    }

    /**
     * Get user by specified id.
     *
     * @param  int  $id     The user id
     *
     * @return User
     */
    public function findById($id)
    {
        return $this->userProvider->findById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByGroupName($groupName)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function findByGroupId($groupId)
    {

    }

    /**
     * Get user by user login.
     *
     * @param  string $login The user login (default is email address)
     *
     * @return User
     */
    public function findByLogin($login)
    {
        return $this->userProvider->findByLogin($login);
    }

    /**
     * Get user by matching credential (login and password).
     *
     * @param  array  $credential     Pair of login and password
     *
     * @return User
     */
    public function findByCredential(array $credential)
    {
        return $this->userProvider->findByCredential($credential);
    }

    public function findByKeyword($keyword)
    {

    }

    /**
     * Delete user by id.
     *
     * @param  int  $id     The user id
     *
     * @return boolean
     */
    public function delete($id)
    {
        return  $this->findById($id)->delete();
    }

    /**
     * Create new user instance in the database.
     *
     * @param  array  $userData     User data information.
     *
     * @return User
     */
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

        return $user;
    }

    /**
     * Update user information.
     *
     * @param  int      $userId   The user id
     * @param  array    $userData The user data
     *
     * @return User
     */
    public function update($userId, array $userData)
    {
        $user   = $this->sentry->findUserById($userId);
        $groups = isset($userData['groups']) ? $userData['groups'] : [];

        if (isset($userData['password']) && $userData['password'] !== $userData['confirm_password']) {
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

        if (isset($userData['permissions'])) {
            $userPermissions = $userData['permissions'];
            $userData['permissions'] = [];

            foreach ($userPermissions as $permission) {
                $userData['permissions'][$permission] = 1;
            }
        }

        $permissions = $this->permissionRepo->findAll();
        $defaultPermissions = [];

        foreach ($permissions as $permission) {
            $defaultPermissions[$permission->name] = 0;
        }

        $userData['permissions'] = array_merge($defaultPermissions, $userData['permissions']);

        $user->update($userData);

        foreach ($groups as $groupId) {
            $group = $this->sentry->findGroupById($groupId);

            $user->addGroup($group);
        }

        return $user;
    }

    /**
     * Get current logged in user.
     *
     * @return User|null
     */
    public function getCurrentUser()
    {
        return $this->sentry->getUser();
    }

    /**
     * Create new query for datatable processing.
     *
     */
    public function createDatatableQuery()
    {
        $currentUser = $this->getCurrentUser();

        return $this->userProvider->createModel()->where('id', '<>', $currentUser->id);
    }
}

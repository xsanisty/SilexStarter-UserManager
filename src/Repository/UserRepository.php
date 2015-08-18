<?php

namespace Xsanisty\UserManager\Repository;

use Exception;
use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\ProviderInterface;
use Xsanisty\Datatable\DatatableResponseBuilder;

class UserRepository implements UserRepositoryInterface
{
    protected $sentry;
    protected $datatable;
    protected $userProvider;

    public function __construct(Sentry $sentry, ProviderInterface $userProvider, DatatableResponseBuilder $datatable)
    {
        $this->userProvider = $userProvider;
        $this->datatable    = $datatable;
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

        if ($userData['password'] === $userData['confirm_password']) {
            unset($userData['confirm_password']);
        } else {
            throw new Exception("Password and confirmation password mismatch", 1);
        }

        if (!$userData['password']) {
            unset($userData['password']);
        }

        unset($userData['groups']);

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

    public function createDatatableResponse()
    {
        $currentUser    = $this->getCurrentUser();
        $hasEditAccess  = $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.user.edit']) : false;
        $hasDeleteAccess= $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.user.delete']) : false;

        return  $this
                ->datatable
                ->of(
                    $this->userProvider
                         ->createModel()
                         ->where('id', '<>', $currentUser->id)
                )
                ->setColumn(['first_name', 'last_name', 'email', 'activated', 'last_login', 'id'])
                ->setFormatter(
                    function ($row) use ($hasEditAccess, $hasDeleteAccess) {
                        $editButton     = $hasEditAccess
                                        ? '<button href="'.
                                            Url::to('usermanager.user.edit', ['id' => $row->id]).
                                            '" class="btn btn-xs btn-primary btn-edit" style="margin-right: 5px">edit</button>'
                                        : '';
                        $deleteButton   = $hasDeleteAccess
                                        ? '<button href="'.
                                            Url::to('usermanager.user.delete', ['id' => $row->id]).
                                            '" class="btn btn-xs btn-danger btn-delete" style="margin-right: 5px">delete</button>'
                                        : '';

                        return [
                            $row->first_name,
                            $row->last_name,
                            $row->email,
                            $row->activated == 1 ? '<span class="label label-success">active</span>' : '<span class="label label-danger">suspended</span>',
                            $row->last_login ? $row->last_login->format('Y-m-d H:i') : '',
                            $editButton.$deleteButton
                        ];
                    }
                )
                ->make();
    }
}

<?php

namespace Xsanisty\UserManager\Repository;

use Cartalyst\Sentry\Groups\ProviderInterface;
use Xsanisty\Datatable\DatatableResponseBuilder;

class GroupRepository
{

    protected $groupProvider;
    protected $datatable;
    protected $userRepository;

    public function __construct(ProviderInterface $groupProvider, UserRepository $userRepository, DatatableResponseBuilder $datatable)
    {
        $this->groupProvider    = $groupProvider;
        $this->datatable        = $datatable;
        $this->userRepository   = $userRepository;
    }

    public function findById($id)
    {
        return $this->groupProvider->findById($id);
    }

    public function findByName($name)
    {
        return $this->groupProvider->findByName($name);
    }

    public function findAll()
    {
        return $this->groupProvider->findAll();
    }

    public function create(array $groupData)
    {
        if (isset($groupData['permissions'])) {
            $permissions = $groupData['permissions'];
            $groupData['permissions'] = [];

            foreach ($permissions as $perm) {
                $groupData['permissions'][$perm] = 1;
            }
        }

        return $this->groupProvider->create($groupData);
    }

    public function update($id, $groupData)
    {
        $group = $this->findById($id);

        if (isset($groupData['permissions'])) {
            $permissions = $groupData['permissions'];
            $groupData['permissions'] = [];

            foreach ($permissions as $perm) {
                $groupData['permissions'][$perm] = 1;
            }
        }

        return $group->update($groupData);
    }

    public function delete($id)
    {
        return $this->groupProvider->findById($id)->delete();
    }

    public function createDatatableResponse()
    {
        $currentUser    = $this->userRepository->getCurrentUser();
        $hasEditAccess  = $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.group.edit']) : false;
        $hasDeleteAccess= $currentUser ? $currentUser->hasAnyAccess(['admin', 'usermanager.group.delete']) : false;

        return $this->datatable
                    ->of($this->groupProvider->createModel())
                    ->setColumn(['name', 'description', 'id'])
                    ->setFormatter(
                        function ($row) use ($hasEditAccess, $hasDeleteAccess) {
                            $editButton     = $hasEditAccess
                                            ? '<button href="'.
                                                Url::to('usermanager.group.edit', ['id' => $row->id]).
                                                '" class="btn btn-xs btn-primary btn-edit" style="margin-right: 5px">edit</button>'
                                            : '';
                            $deleteButton   = $hasDeleteAccess
                                            ? '<button href="'.
                                                Url::to('usermanager.group.delete', ['id' => $row->id]).
                                                '" class="btn btn-xs btn-danger btn-delete" style="margin-right: 5px">delete</button>'
                                            : '';
                            return [
                                $row->name,
                                $row->description,
                                $editButton.$deleteButton
                            ];
                        }
                    )
                    ->make();
    }
}

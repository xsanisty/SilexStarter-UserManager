<?php

namespace Xsanisty\UserManager\Repository;

use Cartalyst\Sentry\Groups\ProviderInterface;
use Xsanisty\Datatable\DatatableResponseBuilder;

class GroupRepository implements GroupRepositoryInterface
{

    protected $groupProvider;

    public function __construct(ProviderInterface $groupProvider)
    {
        $this->groupProvider    = $groupProvider;
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

    public function createDatatableQuery()
    {
        return $this->groupProvider->createModel();
    }
}

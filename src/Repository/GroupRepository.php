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
        $groupData = $this->prepareData($groupData);

        return $this->groupProvider->create($groupData);
    }

    public function update($id, $groupData)
    {
        $group      = $this->findById($id);
        $groupData  = $this->prepareData($groupData);

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

    protected function prepareData(array $data)
    {
        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            $data['permissions'] = [];

            foreach ($permissions as $perm) {
                $data['permissions'][$perm] = 1;
            }
        }

        return $data;
    }
}

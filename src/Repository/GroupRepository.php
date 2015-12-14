<?php

namespace Xsanisty\UserManager\Repository;

use Cartalyst\Sentry\Groups\ProviderInterface;
use Xsanisty\UserManager\Contract\GroupRepositoryInterface;

class GroupRepository implements GroupRepositoryInterface
{

    protected $groupProvider;

    public function __construct(ProviderInterface $groupProvider)
    {
        $this->groupProvider    = $groupProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        return $this->groupProvider->findById($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return $this->groupProvider->findByName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->groupProvider->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $groupData)
    {
        $groupData = $this->prepareData($groupData);

        return $this->groupProvider->create($groupData);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, $groupData)
    {
        $group      = $this->findById($id);
        $groupData  = $this->prepareData($groupData);

        return $group->update($groupData);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->groupProvider->findById($id)->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function createDatatableQuery()
    {
        return $this->groupProvider->createModel();
    }

    /**
     * Prepare group data structure.
     *
     * @param  array  $data Thhe group information
     *
     * @return array        Updated group information
     */
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

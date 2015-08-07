<?php

namespace Xsanisty\UserManager\Repository;

use Cartalyst\Sentry\Groups\ProviderInterface;
use Xsanisty\Datatable\DatatableResponseBuilder;

class GroupRepository
{

    protected $groupProvider;
    protected $datatable;

    public function __construct(ProviderInterface $groupProvider, DatatableResponseBuilder $datatable)
    {
        $this->groupProvider = $groupProvider;
        $this->datatable = $datatable;
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
}

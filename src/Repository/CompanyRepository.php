<?php

namespace Xsanisty\UserManager\Repository;

use Exception;

use Xsanisty\UserManager\Model\Company;
use Xsanisty\UserManager\Contract\CompanyRepositoryInterface;

class CompanyRepository implements CompanyRepositoryInterface
{
    protected $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        return $this->company->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return $this->company->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $this->company->newQuery()
            ->where('id', '=', $id)
            ->update($data);

        return $this->company->newQuery()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->company->newQuery()->where('id', '=', $id)->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function createDatatableQuery()
    {
        return $this->company->newQuery();
    }
}

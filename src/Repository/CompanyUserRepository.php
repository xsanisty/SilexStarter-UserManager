<?php

namespace Xsanisty\UserManager\Repository;

use Exception;
use Cartalyst\Sentry\Users\UserInterface;
use Xsanisty\UserManager\Model\CompanyUser;
use Xsanisty\UserManager\Contract\CompanyUserRepositoryInterface;

class CompanyUserRepository implements CompanyUserRepositoryInterface
{
    protected $company_user;

    public function __construct(CompanyUser $company_user)
    {
        $this->company_user = $company_user;
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        return $this->company_user->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return $this->company_user->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $this->company_user->newQuery()
            ->where('id', '=', $id)
            ->update($data);

        return $this->company_user->newQuery()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->company_user->newQuery()->where('id', '=', $id)->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function createDatatableQuery(UserInterface $user)
    {
        return $this->company_user->newQuery()->with('company')->where('user_id', '=', $user->getId());
    }
}

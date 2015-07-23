<?php

namespace Xsanisty\UserManager\Repository;

use Cartalyst\Sentry\Users\ProviderInterface;
use Xsanisty\Datatable\DatatableResponseBuilder;

class UserRepository
{
    protected $datatable;
    protected $userProvider;

    public function __construct(ProviderInterface $userProvider, DatatableResponseBuilder $datatable)
    {
        $this->userProvider = $userProvider;
        $this->datatable    = $datatable;
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
        return $this->findById($id)->delete();
    }

    public function createDatatableResponse()
    {
        return $this->datatable
                    ->of($this->userProvider->createModel())
                    ->setColumn(['first_name', 'last_name', 'email', 'id'])
                    ->setFormatter(
                        function ($row) {
                            return [
                                $row->first_name,
                                $row->last_name,
                                $row->email,
                                '<a href="'.Url::to('usermanager.user.delete', ['id' => $row->id]).'" class="btn btn-xs btn-danger user-delete" style="margin-right: 5px">delete</a>'.
                                '<a href="'.Url::to('usermanager.user.edit', ['id' => $row->id]).'" class="btn btn-xs btn-primary user-edit" style="margin-right: 5px">edit</a>'
                            ];
                        }
                    )
                    ->make();
    }
}

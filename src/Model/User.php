<?php

namespace Xsanisty\UserManager\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentry\Users\Eloquent\User as SentryUser;

class User extends SentryUser
{
    public function isSuperUser()
    {
        return $this->hasPermission('admin');
    }

    public function getPermissionsAttribute($permissions)
    {
        $company = $this->getActiveCompany();
        return array_merge(parent::getPermissionsAttribute($permissions), []);
    }

    public function companies()
    {
        return $this->belongsToMany('Xsanisty\UserManager\Model\Company', 'companies_users', 'user_id', 'company_id')
                    ->withPivot('primary', 'active', 'admin', 'permissions');
    }

    public function getActiveCompany()
    {
        return $this->companies()->where('active', '=', 1)->first();
    }

    public function newPivot(Model $parent, array $attributes, $table, $exists, $using = nul)
    {
        if ($parent instanceof \Xsanisty\UserManager\Model\Company) {
            return new CompanyUserPivot($parent, $attributes, $table, $exists);
        } else {
            return new Pivot($parent, $attributes, $table, $exists);
        }
    }
}

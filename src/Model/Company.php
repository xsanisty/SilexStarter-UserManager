<?php

namespace Xsanisty\UserManager\Model;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = ['id'];

    public function newPivot(Model $parent, array $attributes, $table, $exists, $using = null)
    {
        return new CompanyUserPivot($parent, $attributes, $table, $exists, $using);
    }
}

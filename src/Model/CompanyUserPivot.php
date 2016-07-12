<?php

namespace Xsanisty\UserManager\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyUserPivot extends Pivot
{
    protected $casts = [
        'primary'       => 'boolean',
        'admin'         => 'boolean',
        'active'        => 'boolean',
        'permissions'   => 'array',
    ];
}

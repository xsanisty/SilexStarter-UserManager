<?php

namespace Xsanisty\UserManager\Model;

class CompanyUser extends Model
{
    protected $table    = 'companies_users';
    protected $guarded  = ['id'];
    protected $casts    = [
        'primary'       => 'boolean',
        'admin'         => 'bolean',
        'active'        => 'boolean',
        'permissions'   => 'array',
    ];

    public function company()
    {
        return $this->belongsTo('Xsanisty\UserManager\Model\Company');
    }
}

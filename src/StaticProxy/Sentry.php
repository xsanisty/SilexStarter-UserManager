<?php

namespace Xsanisty\UserManager\StaticProxy;

use XStatic\StaticProxy;

class Sentry extends StaticProxy
{
    public static function getInstanceIdentifier()
    {
        return 'sentry';
    }
}

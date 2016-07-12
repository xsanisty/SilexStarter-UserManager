<?php

namespace Xsanisty\UserManager\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Xsanisty\UserManager\Model\CompanyUser;
use Xsanisty\UserManager\Repository\CompanyUserRepository;

class CompanyUserRepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['Xsanisty\UserManager\Repository\CompanyUserRepository'] = $app->share(
            function (Application $app) {
                return new CompanyUserRepository(new CompanyUser);
            }
        );
        $app->bind('Xsanisty\UserManager\Contract\CompanyUserRepositoryInterface', 'Xsanisty\UserManager\Repository\CompanyUserRepository');
    }

    public function boot(Application $app)
    {
    }
}

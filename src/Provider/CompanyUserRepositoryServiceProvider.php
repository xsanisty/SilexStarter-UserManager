<?php

namespace Xsanisty\UserManager\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Xsanisty\UserManager\Model\CompanyUser;
use Xsanisty\UserManager\Repository\CompanyUserRepository;

class CompanyUserRepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['Xsanisty\UserManager\Repository\CompanyUserRepository'] = $app->share(
            function (Container $app) {
                return new CompanyUserRepository(new CompanyUser);
            }
        );
        $app->bind('Xsanisty\UserManager\Contract\CompanyUserRepositoryInterface', 'Xsanisty\UserManager\Repository\CompanyUserRepository');
    }

    public function boot(Container $app)
    {
    }
}

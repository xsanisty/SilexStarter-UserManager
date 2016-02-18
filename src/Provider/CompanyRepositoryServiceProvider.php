<?php

namespace Xsanisty\UserManager\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Xsanisty\UserManager\Model\Company;
use Xsanisty\UserManager\Repository\CompanyRepository;

class CompanyRepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['Xsanisty\UserManager\Repository\CompanyRepository'] = $app->share(
            function (Application $app) {
                return new CompanyRepository(new Company);
            }
        );
        $app->bind('Xsanisty\UserManager\Contract\CompanyRepositoryInterface', 'Xsanisty\UserManager\Repository\CompanyRepository');
    }

    public function boot(Application $app)
    {
    }
}

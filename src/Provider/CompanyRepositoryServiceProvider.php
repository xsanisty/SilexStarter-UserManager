<?php

namespace Xsanisty\UserManager\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Xsanisty\UserManager\Model\Company;
use Xsanisty\UserManager\Repository\CompanyRepository;

class CompanyRepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['Xsanisty\UserManager\Repository\CompanyRepository'] = $app->share(
            function (Container $app) {
                return new CompanyRepository(new Company);
            }
        );
        $app->bind('Xsanisty\UserManager\Contract\CompanyRepositoryInterface', 'Xsanisty\UserManager\Repository\CompanyRepository');
    }

    public function boot(Container $app)
    {
    }
}

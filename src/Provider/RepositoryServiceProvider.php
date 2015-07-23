<?php

namespace Xsanisty\UserManager\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Xsanisty\UserManager\Repository\UserRepository;
use Xsanisty\UserManager\Repository\GroupRepository;
use Xsanisty\UserManager\Repository\PermissionRepository;

class RepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['Xsanisty\UserManager\Repository\UserRepository'] = $app->share(
            function (Application $app) {
                return new UserRepository($app['sentry.user'], $app['datatable']);
            }
        );

        $app['Xsanisty\UserManager\Repository\GroupRepository'] = $app->share(
            function (Application $app) {
                return new GroupRepository($app['sentry']);
            }
        );
    }

    public function boot(Application $app)
    {

    }
}

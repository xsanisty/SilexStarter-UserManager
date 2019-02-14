<?php

namespace Xsanisty\UserManager\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Xsanisty\UserManager\Model\Permission;
use Xsanisty\UserManager\Repository\UserRepository;
use Xsanisty\UserManager\Repository\GroupRepository;
use Xsanisty\UserManager\Repository\PermissionRepository;

class RepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['Xsanisty\UserManager\Repository\UserRepository'] = $app->share(
            function (Container $app) {
                return new UserRepository(
                    $app['sentry'],
                    $app['sentry.user'],
                    $app['Xsanisty\UserManager\Contract\PermissionRepositoryInterface']
                );
            }
        );
        $app->bind('Xsanisty\UserManager\Contract\UserRepositoryInterface', 'Xsanisty\UserManager\Repository\UserRepository');

        $app['Xsanisty\UserManager\Repository\GroupRepository'] = $app->share(
            function (Container $app) {
                return new GroupRepository($app['sentry.group']);
            }
        );
        $app->bind('Xsanisty\UserManager\Contract\GroupRepositoryInterface', 'Xsanisty\UserManager\Repository\GroupRepository');

        $app['Xsanisty\UserManager\Repository\PermissionRepository'] = $app->share(
            function (Container $app) {
                return new PermissionRepository(new Permission);
            }
        );
        $app->bind('Xsanisty\UserManager\Contract\PermissionRepositoryInterface', 'Xsanisty\UserManager\Repository\PermissionRepository');
    }

    public function boot(Container $app)
    {

    }
}

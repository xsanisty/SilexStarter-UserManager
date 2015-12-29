<?php

namespace Xsanisty\UserManager\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Intervention\Image\ImageManager;

class ImageServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['image'] = $app->share(
            function (Application $app) {
                return new ImageManager;
            }
        );

        $app->bind('Intervention\Image\ImageManager', 'image');
    }

    public function boot(Application $app)
    {

    }
}

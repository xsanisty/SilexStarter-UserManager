<?php

namespace Xsanisty\UserManager\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Intervention\Image\ImageManager;

class ImageServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['image'] = $app->share(
            function (Container $app) {
                return new ImageManager;
            }
        );

        $app->bind('Intervention\Image\ImageManager', 'image');
    }

    public function boot(Container $app)
    {

    }
}

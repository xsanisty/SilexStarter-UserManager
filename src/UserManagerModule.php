<?php

/**
 * This is user module, also works as sample module to show you how develop module as composer package
 */
namespace Xsanisty\UserManager;

use Silex\Application;
use SilexStarter\Module\ModuleInfo;
use SilexStarter\Module\ModuleResource;
use SilexStarter\Contracts\ModuleProviderInterface;

class UserManagerModule implements ModuleProviderInterface
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getInfo()
    {
        return new ModuleInfo(
            [
                'name'          => 'SilexStarter User Manager',
                'author_name'   => 'Xsanisty Development Team',
                'author_email'  => 'developers@xsanisty.com',
                'repository'    => 'https://github.com/xsanisty/SilexStarter-UserManager',
            ]
        );
    }

    public function getModuleIdentifier()
    {
        return 'silexstarter-usermanager';
    }

    public function getRequiredModules()
    {
        return ['silexstarter-dashboard'];
    }

    public function getResources()
    {
        return new ModuleResource(
            [
                'routes'        => 'Resources/routes.php',
                'views'         => 'Resources/views',
                'controllers'   => 'Controller',
                'commands'      => 'Command',
                'services'      => ''
            ]
        );
    }

    public function register()
    {
    }

    public function boot()
    {
        $this->registerSidebarMenu();
        $this->registerNavbarMenu();
    }

    protected function registerNavbarMenu()
    {
        $menu = Menu::get('admin_navbar')->createItem(
            'user',
            [
                'icon'  => 'user',
                'url'   => '#user',
            ]
        );

        $menu->addChildren(
            'user-header',
            [
                'label' => 'My Account',
                'class' => 'header'
            ]
        );

        $menu->addChildren(
            'user-header',
            [
                'label' => 'Settings',
                'class' => 'link',
                'icon'  => 'cog',
                'url'   => Url::to('usermanager.settings')
            ]
        );
    }

    protected function registerSidebarMenu()
    {

    }
}

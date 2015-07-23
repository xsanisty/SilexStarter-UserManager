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
        return ['silexstarter-dashboard', 'silexstarter-datatable'];
    }

    public function getResources()
    {
        return new ModuleResource(
            [
                'routes'        => 'Resources/routes.php',
                'views'         => 'Resources/views',
                'config'        => 'Resources/config',
                'assets'        => 'Resources/assets',
                'controllers'   => 'Controller',
                'commands'      => 'Command',
            ]
        );
    }

    public function register()
    {
        $this->app->registerServices(
            $this->app['config']['@silexstarter-usermanager.services']
        );
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
                'label' => 'Account',
                'class' => 'header'
            ]
        );

        $menu->addChildren('user-header-divider', [ 'class' => 'divider' ]);

        $menu->addChildren(
            'my-account',
            [
                'label' => 'My Account',
                'class' => 'link',
                'icon'  => 'user',
                'url'   => Url::to('usermanager.my_account')
            ]
        );

        $menu->addChildren(
            'user-settings',
            [
                'label' => 'Settings',
                'class' => 'link',
                'icon'  => 'cog',
                'url'   => Url::to('usermanager.settings')
            ]
        );

        $menu->addChildren('logout-divider', [ 'class' => 'divider' ]);

        $menu->addChildren(
            'user-logout',
            [
                'label' => 'Logout',
                'class' => 'link',
                'icon'  => 'sign-out',
                'url'   => Url::to('admin.logout')
            ]
        );
    }

    protected function registerSidebarMenu()
    {
        $menu   = Menu::get('admin_sidebar')->createItem(
            'user-manager',
            [
                'icon'  => 'users',
                'label' => 'User and Group',
                'url'   => '#'
            ]
        );

        $menu->addChildren(
            'manage-user',
            [
                'icon'  => 'user',
                'label' => 'Users',
                'title' => 'Manage Users',
                'url'   => Url::to('usermanager.user.index')
            ]
        );

        $menu->addChildren(
            'manage-group',
            [
                'icon'  => 'users',
                'label' => 'Groups',
                'title' => 'Manage Groups',
                'url'   => Url::to('usermanager.group.index')
            ]
        );

        $menu->addChildren(
            'manage-permission',
            [
                'icon'  => 'th-list',
                'label' => 'Permissions',
                'title' => 'Manage Permissions',
                'url'   => Url::to('usermanager.permission.index')
            ]
        );
    }
}

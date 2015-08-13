<?php

/**
 * This is user module, also works as sample module to show you how develop module as composer package
 */
namespace Xsanisty\UserManager;

use Silex\Application;
use SilexStarter\Module\ModuleInfo;
use SilexStarter\Module\ModuleResource;
use SilexStarter\Contracts\ModuleProviderInterface;
use Xsanisty\Admin\DashboardModule;

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
                'migrations'    => 'Migration'
            ]
        );
    }

    public function register()
    {
        $this->app->registerServices(
            $this->app['config']['@silexstarter-usermanager.services']
        );

        $provider = $this;

        $this->app['dispatcher']->addListener(
            DashboardModule::INIT,
            function () use ($provider) {
                $provider->registerSidebarMenu();
                $provider->registerNavbarMenu();
            },
            2
        );
    }

    public function boot()
    {
    }


    /**
     * Register menu item to navbar menu
     */
    protected function registerNavbarMenu()
    {
        $user   = $this->app['sentry']->getUser();
        $name   = $user ? $user->first_name.' '.$user->last_name : '';
        $email  = $user ? $user->email : '';
        $name   = trim($name) ? $name : $email;


        $menu = $this->app['menu_manager']->get('admin_navbar')->createItem(
            'user',
            [
                'icon'  => 'user',
                'url'   => '#user',
            ]
        );

        $menu->addChildren(
            'user-header',
            [
                'label' => $name,
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
                'url'   => 'usermanager.my_account'
            ]
        );

        $menu->addChildren('logout-divider', [ 'class' => 'divider' ]);

        $menu->addChildren(
            'user-logout',
            [
                'label' => 'Logout',
                'class' => 'link',
                'icon'  => 'sign-out',
                'url'   => 'admin.logout'
            ]
        );
    }

    /**
     * Register menu item to sidebar menu
     */
    protected function registerSidebarMenu()
    {
        $menu   = $this->app['menu_manager']->get('admin_sidebar')->createItem(
            'user-manager',
            [
                'icon'          => 'users',
                'label'         => 'User and Group',
                'url'           => '#',
                'permission'    => ['usermanager', 'usermanager.user.read', 'usermanager.group.read', 'usermanager.permission.read']
            ]
        );

        $menu->addChildren(
            'manage-user',
            [
                'icon'          => 'user',
                'label'         => 'Users',
                'title'         => 'Manage Users',
                'url'           => 'usermanager.user.index',
                'permission'    => ['usermanager.user.read']
            ]
        );

        $menu->addChildren(
            'manage-group',
            [
                'icon'          => 'users',
                'label'         => 'Groups',
                'title'         => 'Manage Groups',
                'url'           => 'usermanager.group.index',
                'permission'    => ['usermanager.group.read']
            ]
        );

        $menu->addChildren(
            'manage-permission',
            [
                'icon'          => 'th-list',
                'label'         => 'Permissions',
                'title'         => 'Manage Permissions',
                'url'           => 'usermanager.permission.index',
                'permission'    => ['usermanager.permission.read']
            ]
        );
    }
}

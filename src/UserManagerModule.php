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

    /**
     * {@inheritdoc}
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredModules()
    {
        return ['silexstarter-dashboard', 'silexstarter-datatable'];
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo()
    {
        return new ModuleInfo(
            [
                'name'          => 'SilexStarter User Manager',
                'author_name'   => 'Xsanisty Development Team',
                'author_email'  => 'developers@xsanisty.com',
                'repository'    => 'https://github.com/xsanisty/SilexStarter-UserManager',
                'description'   => 'Provide functionality to manage user, group/role, and permission'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleIdentifier()
    {
        return 'silexstarter-usermanager';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getRequiredPermissions()
    {
        return [
            'usermanager.user.read'         => 'Read user list in the database',
            'usermanager.user.create'       => 'Create new user in the database',
            'usermanager.user.edit'         => 'Edit information of existing user',
            'usermanager.user.delete'       => 'Remove user from the database',
            'usermanager.group.read'        => 'Read user group list in the database',
            'usermanager.group.create'      => 'Create new group in the database',
            'usermanager.group.edit'        => 'Edit information of existing group',
            'usermanager.group.delete'      => 'Remove group from the database',
            'usermanager.permission.read'   => 'Read permission list in the database',
            'usermanager.permission.create' => 'Create new permission in the database',
            'usermanager.permission.edit'   => 'Edit existing permission',
            'usermanager.permission.delete' => 'Remove permission from the database',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {

    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
    }


    /**
     * Register menu item to navbar menu
     */
    protected function registerNavbarMenu()
    {
        $user   = $this->app['user'];
        $name   = $user ? $user->first_name.' '.$user->last_name : '';
        $email  = $user ? $user->email : '';
        $name   = trim($name) ? $name : $email;
        $menu   = $this->app['menu_manager']->get('admin_navbar')->getItem('user');

        $menu->addChildren(
            'user-account',
            [
                'label'     => 'My Account',
                'icon'      => 'user',
                'url'       => Url::to('usermanager.my_account'),
                'meta'      => ['type' => 'link'],
                'options'   => ['position' => 'after:user-header-divider']
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
                'url'           => Url::to('usermanager.user.index'),
                'permission'    => ['usermanager.user.read']
            ]
        );

        $menu->addChildren(
            'manage-group',
            [
                'icon'          => 'users',
                'label'         => 'Groups',
                'title'         => 'Manage Groups',
                'url'           => Url::to('usermanager.group.index'),
                'permission'    => ['usermanager.group.read']
            ]
        );

        $menu->addChildren(
            'manage-permission',
            [
                'icon'          => 'th-list',
                'label'         => 'Permissions',
                'title'         => 'Manage Permissions',
                'url'           => Url::to('usermanager.permission.index'),
                'permission'    => ['usermanager.permission.read']
            ]
        );
    }
}

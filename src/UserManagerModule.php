<?php

/**
 * This is user module, also works as sample module to show you how develop module as composer package
 */
namespace Xsanisty\UserManager;

use Silex\Application;
use SilexStarter\Module\ModuleInfo;
use SilexStarter\Module\ModuleResource;
use SilexStarter\Module\ModuleProvider;
use Xsanisty\Admin\DashboardModule;

class UserManagerModule extends ModuleProvider
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        $this->info = new ModuleInfo(
            [
                'name'          => 'SilexStarter User Manager',
                'author_name'   => 'Xsanisty Development Team',
                'author_email'  => 'developers@xsanisty.com',
                'repository'    => 'https://github.com/xsanisty/SilexStarter-UserManager',
                'description'   => 'Provide functionality to manage user, group/role, and permission'
            ]
        );

        $this->resources = new ModuleResource(
            [
                'routes'        => 'Resources/routes.php',
                'views'         => 'Resources/views',
                'config'        => 'Resources/config',
                'assets'        => 'Resources/assets',
                'migrations'    => 'Resources/migrations',
                'controllers'   => 'Controller',
                'commands'      => 'Command',
            ]
        );
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
    public function getModuleIdentifier()
    {
        return 'silexstarter-usermanager';
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
            'usermanager.company.read'      => 'Read company list in the database',
            'usermanager.company.create'    => 'Create new company in the database',
            'usermanager.company.edit'      => 'Edit existing company info',
            'usermanager.company.delete'    => 'Remove company from database',
            'usermanager.company.admin'     => 'Company administrator, manage company info and user',
        ];
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
            'my-account',
            [
                'label'     => 'My Account',
                'icon'      => 'user',
                'url'       => Url::to('usermanager.my_account'),
                'meta'      => ['type' => 'link'],
                'options'   => ['position' => 'after:user-header-divider']
            ]
        );

        if ($this->app['config']->get('@silexstarter-usermanager.config.enable_switch_tenant')) {
            $menu->addChildren(
                'my-company',
                [
                    'label'     => 'My Company',
                    'icon'      => 'building-o',
                    'url'       => Url::to('usermanager.company_user.index'),
                    'meta'      => ['type' => 'link']
                ]
            );

            $company = $this->app['menu_manager']->get('admin_navbar')->createItem(
                'company',
                [
                    'icon'      => 'building-o',
                    'url'       => '#company',
                    'label'     => 'Switch Company',
                    'meta'      => [
                        'renderer' => 'general-menu-renderer'
                    ],
                    'options'   => [
                        'position'  => 'before:user',
                    ]
                ]
            );

            $company->addChildren(
                'my-company',
                [
                    'label'     => 'My Company',
                    'icon'      => 'building-o',
                    'url'       => Url::to('usermanager.company_user.index'),
                    'meta'      => ['type' => 'link']
                ]
            );
        }
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

        $menu->addChildren(
            'manage-company',
            [
                'icon'          => 'building-o',
                'label'         => 'Companies',
                'title'         => 'Manage Companies',
                'url'           => Url::to('usermanager.company.index'),
                'permission'    => ['usermanager.company.read']
            ]
        );
    }
}

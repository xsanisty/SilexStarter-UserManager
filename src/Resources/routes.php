<?php

Route::group(
    '/admin',
    function () {
        Route::get(
            '/user/settings',
            'Xsanisty\UserManager\Controller\AccountController:settings',
            ['as' => 'usermanager.settings']
        );

        Route::get(
            '/my_account',
            'Xsanisty\UserManager\Controller\AccountController:myAccount',
            ['as' => 'usermanager.my_account']
        );


        Route::resource(
            '/user',
            'Xsanisty\UserManager\Controller\UserController',
            ['as' => 'usermanager.user']
        );

        Route::post(
            '/user/datatable',
            'Xsanisty\UserManager\Controller\UserController:datatable',
            ['as' => 'usermanager.user.datatable']
        );

        Route::resource(
            '/group',
            'Xsanisty\UserManager\Controller\GroupController',
            ['as' => 'usermanager.group']
        );

        Route::post(
            '/group/datatable',
            'Xsanisty\UserManager\Controller\GroupController:datatable',
            ['as' => 'usermanager.group.datatable']
        );

        Route::resource(
            '/permission',
            'Xsanisty\UserManager\Controller\PermissionController',
            ['as' => 'usermanager.permission']
        );

        Route::post(
            '/permission/datatable',
            'Xsanisty\UserManager\Controller\PermissionController:datatable',
            ['as' => 'usermanager.permission.datatable']
        );

    },
    [
        'before'    => 'admin.auth'
    ]
);

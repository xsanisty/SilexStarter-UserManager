<?php

Route::group(
    Config::get('@silexstarter-dashboard.config.admin_prefix'),
    function () {
        Route::get('/user/settings', 'AccountController:settings', ['as' => 'usermanager.settings']);
        Route::get('/my_account', 'AccountController:myAccount', ['as' => 'usermanager.my_account']);
        Route::post('/my_account', 'AccountController:updateAccount', ['as' => 'usermanager.update_account']);

        Route::get('/user/{id}/picture', 'UserController:profilePicture', ['as' => 'usermanager.user.picture']);
        Route::post('/user/datatable', 'UserController:datatable', ['as' => 'usermanager.user.datatable', 'permission' => 'usermanager.user.read']);
        Route::resource(
            '/user',
            'UserController',
            [
                'as' => 'usermanager.user',
                'permission' => 'usermanager.user',
            ]
        );

        Route::post('/group/datatable', 'GroupController:datatable', ['as' => 'usermanager.group.datatable']);
        Route::resource(
            '/group',
            'GroupController',
            [
                'as' => 'usermanager.group',
                'permission' => 'usermanager.group',
            ]
        );

        Route::post('/permission/datatable', 'PermissionController:datatable', ['as' => 'usermanager.permission.datatable']);
        Route::resource(
            '/permission',
            'PermissionController',
            [
                'as' => 'usermanager.permission',
                'permission' => 'usermanager.permission',
            ]
        );

        Route::post('/company/datatable', 'CompanyController:datatable', ['as' => 'usermanager.company.datatable']);
        Route::resource(
            '/company',
            'CompanyController',
            [
                'as' => 'usermanager.company',
                'permission' => 'usermanager.company',
            ]
        );

        Route::resource(
            'user/company',
            'CompanyUserController',
            [
                'as'        => 'usermanager.company_user',
                'except'    => ['create', 'edit']
            ]
        );

        Route::post(
            'user/company/datatable',
            'CompanyUserController:datatable',
            [
                'as'        => 'usermanager.company_user.datatable',
            ]
        );
    },
    [
        'before'    => 'admin.auth',
        'namespace' => 'Xsanisty\UserManager\Controller'
    ]
);

if (Config::get('@silexstarter-usermanager.config.enable_registration')) {
    Route::get(
        '/register',
        'AccountController::register',
        [
            'as'        => 'usermanager.register',
            'namespace' => 'Xsanisty\UserManager\Controller'
        ]
    );
}

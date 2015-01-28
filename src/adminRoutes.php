<?php

use \Admin;

Route::model('user', 'CmsCanvas\Models\User');
Route::model('role', 'CmsCanvas\Models\Role');
Route::model('permission', 'CmsCanvas\Models\Permission');
Route::model('contentType', 'CmsCanvas\Models\Content\Type');
Route::model('contentTypeField', 'CmsCanvas\Models\Content\Type\Field');
Route::model('entry', 'CmsCanvas\Models\Content\Entry');
Route::model('navigation', 'CmsCanvas\Models\Content\Navigation');

Route::group(['prefix' => Admin::getUrlPrefix(), 'before' => 'cmscanvas.auth|cmscanvas.permission', 'permission' => 'ADMIN'], function()
{

    Route::get('/', ['as' => 'admin.index', 'uses' => 'CmsCanvas\Controllers\Admin\DashboardController@getDashboard']);
    Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'CmsCanvas\Controllers\Admin\DashboardController@getDashboard']);

    Route::group(['prefix' => 'user'], function()
    {

        Route::group(['permission' => 'ADMIN_USER_VIEW'], function()
        {
            Route::get('/', ['as' => 'admin.user.users', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@getUsers']);
            Route::post('/', ['as' => 'admin.user.users.post', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@postUsers']);

            Route::post('delete', ['as' => 'admin.user.delete.post', 'permission' => 'ADMIN_USER_DELETE', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@postDelete']);

            Route::get('add', ['as' => 'admin.user.add', 'permission' => 'ADMIN_USER_CREATE', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@getEdit']);
            Route::post('add', ['as' => 'admin.user.add.post', 'permission' => 'ADMIN_USER_CREATE', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@postEdit']);

            Route::get('{user}/edit', ['', 'as' => 'admin.user.edit', 'permission' => 'ADMIN_USER_EDIT', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@getEdit']);
            Route::post('{user}/edit', ['as' => 'admin.user.edit.post', 'permission' => 'ADMIN_USER_EDIT', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@postEdit']);
        });

        Route::group(['prefix' => 'permission', 'permission' => 'ADMIN_PERMISSION_VIEW'], function()
        {

            Route::get('/', ['as' => 'admin.user.permission.permissions', 'uses' => 'CmsCanvas\Controllers\Admin\User\PermissionController@getPermissions']);
            Route::post('/', ['as' => 'admin.user.permission.permissions.post', 'uses' => 'CmsCanvas\Controllers\Admin\User\PermissionController@postPermissions']);

            Route::post('delete', ['as' => 'admin.user.permission.delete.post', 'permission' => 'ADMIN_PERMISSION_DELETE', 'uses' => 'CmsCanvas\Controllers\Admin\User\PermissionController@postDelete']);

            Route::get('add', ['as' => 'admin.user.permission.add', 'permission' => 'ADMIN_PERMISSION_CREATE', 'uses' => 'CmsCanvas\Controllers\Admin\User\PermissionController@getEdit']);
            Route::post('add', ['as' => 'admin.user.permission.add.post', 'permission' => 'ADMIN_PERMISSION_CREATE', 'uses' => 'CmsCanvas\Controllers\Admin\User\PermissionController@postEdit']);

            Route::get('{permission}/edit', ['as' => 'admin.user.permission.edit', 'permission' => 'ADMIN_PERMISSION_EDIT', 'uses' => 'CmsCanvas\Controllers\Admin\User\PermissionController@getEdit']);
            Route::post('{permission}/edit', ['as' => 'admin.user.permission.edit.post', 'permission' => 'ADMIN_PERMISSION_EDIT', 'uses' => 'CmsCanvas\Controllers\Admin\User\PermissionController@postEdit']);

        });

        Route::group(['prefix' => 'role', 'permission' => 'ADMIN_ROLE_VIEW'], function()
        {

            Route::get('/', ['as' => 'admin.user.role.roles', 'uses' => 'CmsCanvas\Controllers\Admin\User\RoleController@getRoles']);
            Route::post('/', ['as' => 'admin.user.role.roles.post', 'uses' => 'CmsCanvas\Controllers\Admin\User\RoleController@postRoles']);

            Route::post('delete', ['as' => 'admin.user.role.delete.post', 'permission' => 'ADMIN_ROLE_DELETE', 'uses' => 'CmsCanvas\Controllers\Admin\User\RoleController@postDelete']);

            Route::get('add', ['as' => 'admin.user.role.add', 'permission' => 'ADMIN_ROLE_CREATE', 'uses' => 'CmsCanvas\Controllers\Admin\User\RoleController@getEdit']);
            Route::post('add', ['as' => 'admin.user.role.add.post', 'permission' => 'ADMIN_ROLE_CREATE', 'uses' => 'CmsCanvas\Controllers\Admin\User\RoleController@postEdit']);

            Route::get('{role}/edit', ['as' => 'admin.user.role.edit', 'permission' => 'ADMIN_ROLE_EDIT', 'uses' => 'CmsCanvas\Controllers\Admin\User\RoleController@getEdit']);
            Route::post('{role}/edit', ['as' => 'admin.user.role.edit.post', 'permission' => 'ADMIN_ROLE_EDIT', 'uses' => 'CmsCanvas\Controllers\Admin\User\RoleController@postEdit']);

        });

    });

    Route::group(array('prefix' => 'content'), function()
    {
        Route::group(array('prefix' => 'navigation'), function()
        {

            Route::get('/', array('as' => 'admin.content.navigation.navigations', 'uses' => 'CmsCanvas\Controllers\Admin\Content\NavigationController@getNavigations'));
            Route::post('/', array('as' => 'admin.content.navigation.navigations.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\NavigationController@postNavigations'));

            Route::get('add', array('as' => 'admin.content.navigation.add', 'uses' => 'CmsCanvas\Controllers\Admin\Content\NavigationController@getEdit'));
            Route::post('add', array('as' => 'admin.content.navigation.add.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\NavigationController@postEdit'));

            Route::get('{navigation}/edit', array('as' => 'admin.content.navigation.edit', 'uses' => 'CmsCanvas\Controllers\Admin\Content\NavigationController@getEdit'));
            Route::post('{navigation}/edit', array('as' => 'admin.content.navigation.edit.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\NavigationController@postEdit'));

            Route::post('delete', array('as' => 'admin.content.navigation.delete.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\NavigationController@postDelete'));

        });

        Route::group(array('prefix' => 'entry'), function()
        {

            Route::get('/', array('as' => 'admin.content.entry.entries', 'uses' => 'CmsCanvas\Controllers\Admin\Content\EntryController@getEntries'));
            Route::post('/', array('as' => 'admin.content.entry.entries.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\EntryController@postEntries'));

            Route::post('delete', array('as' => 'admin.content.entry.delete.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\EntryController@postDelete'));
            Route::post('create-thumbnail', array('as' => 'admin.content.entry.create.thumbnail.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\EntryController@postCreateThumbnail'));

        });

        Route::group(array('prefix' => 'type'), function()
        {

            Route::get('{contentType}/entry/add', array('as' => 'admin.content.entry.add', 'uses' => 'CmsCanvas\Controllers\Admin\Content\EntryController@getEdit'));
            Route::post('{contentType}/entry/add', array('after' =>'cmscanvas.cache.flush', 'as' => 'admin.content.entry.add.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\EntryController@postEdit'));

            Route::get('{contentType}/entry/{entry}/edit', array('as' => 'admin.content.entry.edit', 'uses' => 'CmsCanvas\Controllers\Admin\Content\EntryController@getEdit'));
            Route::post('{contentType}/entry/{entry}/edit', array('after' =>'cmscanvas.cache.flush', 'as' => 'admin.content.entry.edit.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\EntryController@postEdit'));

            Route::get('/', array('as' => 'admin.content.type.types', 'uses' => 'CmsCanvas\Controllers\Admin\Content\TypeController@getTypes'));
            Route::post('/', array('as' => 'admin.content.type.types.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\TypeController@postTypes'));

            Route::post('delete', array('as' => 'admin.content.type.delete.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\TypeController@postDelete'));

            Route::get('add', array('as' => 'admin.content.type.add', 'uses' => 'CmsCanvas\Controllers\Admin\Content\TypeController@getEdit'));
            Route::post('add', array('after' =>'cmscanvas.cache.flush', 'as' => 'admin.content.type.add.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\TypeController@postEdit'));

            Route::get('{contentType}/edit', array('as' => 'admin.content.type.edit', 'uses' => 'CmsCanvas\Controllers\Admin\Content\TypeController@getEdit'));
            Route::post('{contentType}/edit', array('after' =>'cmscanvas.cache.flush', 'as' => 'admin.content.type.edit.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\TypeController@postEdit'));

            Route::group(array('prefix' => '{contentType}/field'), function()
            {

                Route::get('/', array('as' => 'admin.content.type.field.fields', 'uses' => 'CmsCanvas\Controllers\Admin\Content\Type\FieldController@getFields'));
                Route::post('/', array('as' => 'admin.content.type.field.fields.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\Type\FieldController@postFields'));

                Route::post('delete', array('as' => 'admin.content.type.field.delete.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\Type\FieldController@postDelete'));

                Route::get('add', array('as' => 'admin.content.type.field.add', 'uses' => 'CmsCanvas\Controllers\Admin\Content\Type\FieldController@getEdit'));
                Route::post('add', array('as' => 'admin.content.type.field.add.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\Type\FieldController@postEdit'));

                Route::get('{contentTypeField}/edit', array('as' => 'admin.content.type.field.edit', 'uses' => 'CmsCanvas\Controllers\Admin\Content\Type\FieldController@getEdit'));
                Route::post('{contentTypeField}/edit', array('as' => 'admin.content.type.field.edit.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\Type\FieldController@postEdit'));

                Route::post('order', array('as' => 'admin.content.type.field.order.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\Type\FieldController@postOrder'));
                Route::post('settings', array('as' => 'admin.content.type.field.settings.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\Type\FieldController@postSettings'));

            });

        });

    });

});

Route::group(array('prefix' => Admin::getUrlPrefix()), function()
{

    Route::get('user/login', array('as' => 'admin.user.login', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@getLogin'));
    Route::post('user/login', array('as' => 'admin.user.login.post', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@postLogin'));

    Route::get('user/logout', array('as' => 'admin.user.logout', 'uses' => 'CmscCnvas\Controllers\Admin\UserController@getLogout'));

});
<?php

use \Admin;

Route::model('user', 'CmsCanvas\Models\User');
Route::model('role', 'CmsCanvas\Models\Role');
Route::model('permission', 'CmsCanvas\Models\Permission');
Route::model('contentType', 'CmsCanvas\Models\Content\Type');
Route::model('contentTypeField', 'CmsCanvas\Models\Content\Type\Field');
Route::model('entry', 'CmsCanvas\Models\Content\Entry');
Route::model('navigation', 'CmsCanvas\Models\Content\Navigation');
Route::model('navigationItem', 'CmsCanvas\Models\Content\Navigation\Item');
Route::model('language', 'CmsCanvas\Models\Language');
Route::model('revision', 'CmsCanvas\Models\Content\Revision');

Route::group(['prefix' => Admin::getUrlPrefix(), 'middleware' => ['cmscanvas.auth', 'cmscanvas.permission'], 'permission' => 'ADMIN'], function() {

    Route::get('/', ['as' => 'admin.index', 'uses' => 'Admin\DashboardController@getDashboard']);
    Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => 'Admin\DashboardController@getDashboard']);

    Route::group(['prefix' => 'user'], function() {

        Route::group(['permission' => 'ADMIN_USER_VIEW'], function() {

            Route::get('/', ['as' => 'admin.user.users', 'uses' => 'Admin\UserController@getUsers']);
            Route::post('/', ['as' => 'admin.user.users.post', 'uses' => 'Admin\UserController@postUsers']);

            Route::post('delete', ['as' => 'admin.user.delete.post', 'permission' => 'ADMIN_USER_DELETE', 'uses' => 'Admin\UserController@postDelete']);

            Route::get('add', ['as' => 'admin.user.add', 'permission' => 'ADMIN_USER_CREATE', 'uses' => 'Admin\UserController@getEdit']);
            Route::post('add', ['as' => 'admin.user.add.post', 'permission' => 'ADMIN_USER_CREATE', 'uses' => 'Admin\UserController@postEdit']);

            Route::get('{user}/edit', ['', 'as' => 'admin.user.edit', 'permission' => 'ADMIN_USER_EDIT', 'uses' => 'Admin\UserController@getEdit']);
            Route::post('{user}/edit', ['as' => 'admin.user.edit.post', 'permission' => 'ADMIN_USER_EDIT', 'uses' => 'Admin\UserController@postEdit']);

        });

        Route::group(['prefix' => 'permission', 'permission' => 'ADMIN_PERMISSION_VIEW'], function() {

            Route::get('/', ['as' => 'admin.user.permission.permissions', 'uses' => 'Admin\User\PermissionController@getPermissions']);
            Route::post('/', ['as' => 'admin.user.permission.permissions.post', 'uses' => 'Admin\User\PermissionController@postPermissions']);

            Route::post('delete', ['as' => 'admin.user.permission.delete.post', 'permission' => 'ADMIN_PERMISSION_DELETE', 'uses' => 'Admin\User\PermissionController@postDelete']);

            Route::get('add', ['as' => 'admin.user.permission.add', 'permission' => 'ADMIN_PERMISSION_CREATE', 'uses' => 'Admin\User\PermissionController@getEdit']);
            Route::post('add', ['as' => 'admin.user.permission.add.post', 'permission' => 'ADMIN_PERMISSION_CREATE', 'uses' => 'Admin\User\PermissionController@postEdit']);

            Route::get('{permission}/edit', ['as' => 'admin.user.permission.edit', 'permission' => 'ADMIN_PERMISSION_EDIT', 'uses' => 'Admin\User\PermissionController@getEdit']);
            Route::post('{permission}/edit', ['middleware' => ['cmscanvas.flushCache'], 'as' => 'admin.user.permission.edit.post', 'permission' => 'ADMIN_PERMISSION_EDIT', 'uses' => 'Admin\User\PermissionController@postEdit']);

        });

        Route::group(['prefix' => 'role', 'permission' => 'ADMIN_ROLE_VIEW'], function() {

            Route::get('/', ['as' => 'admin.user.role.roles', 'uses' => 'Admin\User\RoleController@getRoles']);
            Route::post('/', ['as' => 'admin.user.role.roles.post', 'uses' => 'Admin\User\RoleController@postRoles']);

            Route::post('delete', ['as' => 'admin.user.role.delete.post', 'permission' => 'ADMIN_ROLE_DELETE', 'uses' => 'Admin\User\RoleController@postDelete']);

            Route::get('add', ['as' => 'admin.user.role.add', 'permission' => 'ADMIN_ROLE_CREATE', 'uses' => 'Admin\User\RoleController@getEdit']);
            Route::post('add', ['as' => 'admin.user.role.add.post', 'permission' => 'ADMIN_ROLE_CREATE', 'uses' => 'Admin\User\RoleController@postEdit']);

            Route::get('{role}/edit', ['as' => 'admin.user.role.edit', 'permission' => 'ADMIN_ROLE_EDIT', 'uses' => 'Admin\User\RoleController@getEdit']);
            Route::post('{role}/edit', ['middleware' => ['cmscanvas.ajax', 'cmscanvas.flushCache'], 'as' => 'admin.user.role.edit.post', 'permission' => 'ADMIN_ROLE_EDIT', 'uses' => 'Admin\User\RoleController@postEdit']);

        });

    });

    Route::group(['prefix' => 'content'], function() {
        Route::group(['prefix' => 'navigation', 'permission' => 'ADMIN_NAVIGATION_VIEW'], function() {

            Route::get('/', ['as' => 'admin.content.navigation.navigations', 'uses' => 'Admin\Content\NavigationController@getNavigations']);
            Route::post('/', ['as' => 'admin.content.navigation.navigations.post', 'uses' => 'Admin\Content\NavigationController@postNavigations']);

            Route::get('add', ['as' => 'admin.content.navigation.add', 'permission' => 'ADMIN_NAVIGATION_CREATE', 'uses' => 'Admin\Content\NavigationController@getEdit']);
            Route::post('add', ['as' => 'admin.content.navigation.add.post', 'permission' => 'ADMIN_NAVIGATION_CREATE', 'uses' => 'Admin\Content\NavigationController@postEdit']);

            Route::get('{navigation}/edit', ['as' => 'admin.content.navigation.edit', 'permission' => 'ADMIN_NAVIGATION_EDIT', 'uses' => 'Admin\Content\NavigationController@getEdit']);
            Route::post('{navigation}/edit', ['as' => 'admin.content.navigation.edit.post', 'permission' => 'ADMIN_NAVIGATION_EDIT', 'uses' => 'Admin\Content\NavigationController@postEdit']);

            Route::get('{navigation}/tree', ['as' => 'admin.content.navigation.tree', 'permission' => 'ADMIN_NAVIGATION_EDIT', 'uses' => 'Admin\Content\NavigationController@getTree']);
            Route::post('{navigation}/tree', ['middleware' => ['cmscanvas.ajax', 'cmscanvas.flushCache'], 'as' => 'admin.content.navigation.tree.post', 'permission' => 'ADMIN_NAVIGATION_EDIT', 'uses' => 'Admin\Content\NavigationController@postTree']);

            Route::post('delete', ['as' => 'admin.content.navigation.delete.post', 'permission' => 'ADMIN_NAVIGATION_DELETE', 'uses' => 'Admin\Content\NavigationController@postDelete']);

            Route::group(['prefix' => '{navigation}/item', 'permission' => 'ADMIN_NAVIGATION_EDIT'], function() {

                Route::get('add', ['as' => 'admin.content.navigation.item.add', 'permission' => 'ADMIN_NAVIGATION_EDIT', 'uses' => 'Admin\Content\Navigation\ItemController@getEdit']);
                Route::post('add', ['middleware' => ['cmscanvas.flushCache'], 'as' => 'admin.content.navigation.item.add.post', 'permission' => 'ADMIN_NAVIGATION_EDIT', 'uses' => 'Admin\Content\Navigation\ItemController@postEdit']);

                Route::get('{navigationItem}/edit', ['as' => 'admin.content.navigation.item.edit', 'permission' => 'ADMIN_NAVIGATION_EDIT', 'uses' => 'Admin\Content\Navigation\ItemController@getEdit']);
                Route::post('{navigationItem}/edit', ['middleware' => ['cmscanvas.flushCache'], 'as' => 'admin.content.navigation.item.edit.post', 'permission' => 'ADMIN_NAVIGATION_EDIT', 'uses' => 'Admin\Content\Navigation\ItemController@postEdit']);

                Route::get('{navigationItem}/delete', ['middleware' => ['cmscanvas.flushCache'], 'as' => 'admin.content.navigation.item.delete', 'permission' => 'ADMIN_NAVIGATION_DELETE', 'uses' => 'Admin\Content\Navigation\ItemController@getDelete']);

            });

        });

        Route::group(['prefix' => 'entry', 'permission' => 'ADMIN_ENTRY_VIEW'], function() {

            Route::get('/', ['as' => 'admin.content.entry.entries', 'uses' => 'Admin\Content\EntryController@getEntries']);
            Route::post('/', ['as' => 'admin.content.entry.entries.post', 'uses' => 'Admin\Content\EntryController@postEntries']);

            Route::post('delete', ['as' => 'admin.content.entry.delete.post', 'permission' => 'ADMIN_ENTRY_DELETE', 'uses' => 'Admin\Content\EntryController@postDelete']);
            Route::post('create-thumbnail', ['middleware' => 'cmscanvas.ajax', 'as' => 'admin.content.entry.create.thumbnail.post', 'uses' => 'Admin\Content\EntryController@postCreateThumbnail']);

        });

        Route::group(['prefix' => 'type'], function() {
            Route::group(['permission' => 'ADMIN_ENTRY_VIEW'], function() {

                Route::get('{contentType}/entry/add', ['as' => 'admin.content.entry.add', 'permission' => 'ADMIN_ENTRY_CREATE', 'uses' => 'Admin\Content\EntryController@getEdit']);
                Route::post('{contentType}/entry/add', ['middleware' =>'cmscanvas.flushCache', 'as' => 'admin.content.entry.add.post', 'permission' => 'ADMIN_ENTRY_CREATE', 'uses' => 'Admin\Content\EntryController@postEdit']);

                Route::get('{contentType}/entry/{entry}/edit', ['as' => 'admin.content.entry.edit', 'permission' => 'ADMIN_ENTRY_EDIT', 'uses' => 'Admin\Content\EntryController@getEdit']);
                Route::post('{contentType}/entry/{entry}/edit', ['middleware' =>'cmscanvas.flushCache', 'as' => 'admin.content.entry.edit.post', 'permission' => 'ADMIN_ENTRY_EDIT', 'uses' => 'Admin\Content\EntryController@postEdit']);

                Route::get('{contentType}/entry/{entry}/edit/revision/{revision}', ['as' => 'admin.content.entry.edit.revision', 'permission' => 'ADMIN_ENTRY_EDIT', 'uses' => 'Admin\Content\EntryController@getEdit']);
                Route::post('{contentType}/entry/{entry}/edit/revision/{revision}', ['middleware' =>'cmscanvas.flushCache', 'as' => 'admin.content.entry.edit.revision.post', 'permission' => 'ADMIN_ENTRY_EDIT', 'uses' => 'Admin\Content\EntryController@postEdit']);

            });

            Route::group(['permission' => 'ADMIN_CONTENT_TYPE_VIEW'], function() {

                Route::get('/', ['as' => 'admin.content.type.types', 'uses' => 'Admin\Content\TypeController@getTypes']);
                Route::post('/', ['as' => 'admin.content.type.types.post', 'uses' => 'Admin\Content\TypeController@postTypes']);

                Route::post('delete', ['as' => 'admin.content.type.delete.post', 'permission' => 'ADMIN_CONTENT_TYPE_DELETE', 'uses' => 'Admin\Content\TypeController@postDelete']);

                Route::get('add', ['as' => 'admin.content.type.add', 'permission' => 'ADMIN_CONTENT_TYPE_CREATE', 'uses' => 'Admin\Content\TypeController@getEdit']);
                Route::post('add', ['middleware' =>'cmscanvas.flushCache', 'as' => 'admin.content.type.add.post', 'permission' => 'ADMIN_CONTENT_TYPE_CREATE', 'uses' => 'Admin\Content\TypeController@postEdit']);

                Route::get('{contentType}/edit', ['as' => 'admin.content.type.edit', 'permission' => 'ADMIN_CONTENT_TYPE_EDIT', 'uses' => 'Admin\Content\TypeController@getEdit']);
                Route::post('{contentType}/edit', ['middleware' => 'cmscanvas.flushCache', 'as' => 'admin.content.type.edit.post', 'permission' => 'ADMIN_CONTENT_TYPE_EDIT', 'uses' => 'Admin\Content\TypeController@postEdit']);
                
            });

            Route::group(['prefix' => '{contentType}/field', 'permission' => 'ADMIN_CONTENT_TYPE_FIELD_VIEW'], function() {

                Route::get('/', ['as' => 'admin.content.type.field.fields', 'uses' => 'Admin\Content\Type\FieldController@getFields']);
                Route::post('/', ['as' => 'admin.content.type.field.fields.post', 'uses' => 'Admin\Content\Type\FieldController@postFields']);

                Route::post('delete', ['as' => 'admin.content.type.field.delete.post', 'permission' => 'ADMIN_CONTENT_TYPE_FIELD_DELETE', 'uses' => 'Admin\Content\Type\FieldController@postDelete']);

                Route::get('add', ['as' => 'admin.content.type.field.add', 'permission' => 'ADMIN_CONTENT_TYPE_FIELD_CREATE', 'uses' => 'Admin\Content\Type\FieldController@getEdit']);
                Route::post('add', ['as' => 'admin.content.type.field.add.post', 'permission' => 'ADMIN_CONTENT_TYPE_FIELD_CREATE', 'uses' => 'Admin\Content\Type\FieldController@postEdit']);

                Route::get('{contentTypeField}/edit', ['as' => 'admin.content.type.field.edit', 'permission' => 'ADMIN_CONTENT_TYPE_FIELD_EDIT', 'uses' => 'Admin\Content\Type\FieldController@getEdit']);
                Route::post('{contentTypeField}/edit', ['as' => 'admin.content.type.field.edit.post', 'permission' => 'ADMIN_CONTENT_TYPE_FIELD_EDIT', 'uses' => 'Admin\Content\Type\FieldController@postEdit']);

                Route::post('order', ['as' => 'admin.content.type.field.order.post', 'permission' => 'ADMIN_CONTENT_TYPE_FIELD_EDIT', 'uses' => 'Admin\Content\Type\FieldController@postOrder']);
                Route::post('settings', ['as' => 'admin.content.type.field.settings.post', 'permission' => 'ADMIN_CONTENT_TYPE_FIELD_EDIT', 'uses' => 'Admin\Content\Type\FieldController@postSettings']);

            });

        });

    });

    Route::group(['prefix' => 'system'], function() {
        Route::get('general-settings', ['as' => 'admin.system.settings.general-settings', 'uses' => 'Admin\System\SettingsController@getGeneralSettings']);
        Route::post('general-settings', ['as' => 'admin.system.settings.general-settings.post', 'uses' => 'Admin\System\SettingsController@postGeneralSettings']);

        Route::post('theme-layouts', ['middleware' => 'cmscanvas.ajax', 'as' => 'admin.system.settings.theme-layouts', 'uses' => 'Admin\System\SettingsController@postThemeLayouts']);

        Route::get('language', ['as' => 'admin.system.language.languages', 'uses' => 'Admin\System\LanguageController@getLanguages']);
        Route::post('language', ['as' => 'admin.system.language.languages.post', 'uses' => 'Admin\System\LanguageController@postLanguages']);

        Route::get('language/add', ['as' => 'admin.system.language.add', 'uses' => 'Admin\System\LanguageController@getEdit']);
        Route::post('language/add', ['as' => 'admin.system.language.add.post', 'uses' => 'Admin\System\LanguageController@postEdit']);

        Route::get('language/{language}/edit', ['as' => 'admin.system.language.edit', 'uses' => 'Admin\System\LanguageController@getEdit']);
        Route::post('language/{language}/edit', ['as' => 'admin.system.language.edit.post', 'uses' => 'Admin\System\LanguageController@postEdit']);

        Route::post('language/delete', ['as' => 'admin.system.language.delete.post', 'uses' => 'Admin\System\LanguageController@postDelete']);

        Route::get('language/{language}/set-default', ['as' => 'admin.system.language.setDefault.post', 'uses' => 'Admin\System\LanguageController@setDefault']);
    });

});

Route::group(['prefix' => Admin::getUrlPrefix()], function() {

    Route::get('user/login', ['as' => 'admin.user.login', 'uses' => 'Admin\UserController@getLogin']);
    Route::post('user/login', ['as' => 'admin.user.login.post', 'uses' => 'Admin\UserController@postLogin']);

    Route::get('user/logout', ['as' => 'admin.user.logout', 'uses' => 'Admin\UserController@getLogout']);

});
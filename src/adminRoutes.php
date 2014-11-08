<?php

use \Admin;

Route::model('user', 'CmsCanvas\Models\User');
Route::model('userGroup', 'CmsCanvas\Models\User\Group');
Route::model('contentType', 'CmsCanvas\Models\Content\Type');
Route::model('contentTypeField', 'CmsCanvas\Models\Content\Type\Field');
Route::model('entry', 'CmsCanvas\Models\Content\Entry');
Route::model('navigation', 'CmsCanvas\Models\Content\Navigation');

Route::group(array('prefix' => Admin::getUrlPrefix(), 'before' => 'cmscanvas.auth'), function()
{

    Route::get('/', array('as' => 'admin.index', 'uses' => 'CmsCanvas\Controllers\Admin\DashboardController@getDashboard'));
    Route::get('dashboard', array('as' => 'admin.dashboard', 'uses' => 'CmsCanvas\Controllers\Admin\DashboardController@getDashboard'));

    Route::group(array('prefix' => 'user'), function()
    {

        Route::get('/', array('as' => 'admin.user.users', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@getUsers'));
        Route::post('/', array('as' => 'admin.user.users.post', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@postUsers'));

        Route::post('delete', array('as' => 'admin.user.delete.post', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@postDelete'));

        Route::get('add', array('as' => 'admin.user.add', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@getAdd'));
        Route::post('add', array('as' => 'admin.user.add.post', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@postAdd'));

        Route::get('{user}/edit', array('as' => 'admin.user.edit', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@getEdit'));
        Route::post('{user}/edit', array('as' => 'admin.user.edit.post', 'uses' => 'CmsCanvas\Controllers\Admin\UserController@postEdit'));

        Route::group(array('prefix' => 'group'), function()
        {

            Route::get('/', array('as' => 'admin.user.group.groups', 'uses' => 'CmsCanvas\Controllers\Admin\User\GroupController@getGroups'));
            Route::post('/', array('as' => 'admin.user.group.groups.post', 'uses' => 'CmsCanvas\Controllers\Admin\User\GroupController@postGroups'));

            Route::post('delete', array('as' => 'admin.user.group.delete.post', 'uses' => 'CmsCanvas\Controllers\Admin\User\GroupController@postDelete'));

            Route::get('add', array('as' => 'admin.user.group.add', 'uses' => 'CmsCanvas\Controllers\Admin\User\GroupController@getAdd'));
            Route::post('add', array('as' => 'admin.user.group.add.post', 'uses' => 'CmsCanvas\Controllers\Admin\User\GroupController@postAdd'));

            Route::get('{userGroup}/edit', array('as' => 'admin.user.group.edit', 'uses' => 'CmsCanvas\Controllers\Admin\User\GroupController@getEdit'));
            Route::post('{userGroup}/edit', array('as' => 'admin.user.group.edit.post', 'uses' => 'CmsCanvas\Controllers\Admin\User\GroupController@postEdit'));

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

            Route::get('add', array('as' => 'admin.content.type.add', 'uses' => 'CmsCanvas\Controllers\Admin\Content\TypeController@getAdd'));
            Route::post('add', array('after' =>'cmscanvas.cache.flush', 'as' => 'admin.content.type.add.post', 'uses' => 'CmsCanvas\Controllers\Admin\Content\TypeController@postAdd'));

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
<?php

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. 
|
*/

Route::filter('cmscanvas.auth', function()
{
    if (Auth::guest()) return Redirect::route('admin.user.login');
});

/*
|--------------------------------------------------------------------------
| Cache Filters
|--------------------------------------------------------------------------
|
| The following filter is used to flush the cache directory
|
*/

Route::filter('cmscanvas.cache.flush', function()
{
    \Cache::flush();
});

/*
|--------------------------------------------------------------------------
| Permission Filters
|--------------------------------------------------------------------------
|
| The following filter is used to verify the user has permission to 
| the requested route 
|
*/

Route::filter('cmscanvas.permission', function($route, $request)
{
    $actions = $route->getAction();
    $permissions = (isset($actions['permission'])) ? (array) $actions['permission'] : array();

    foreach ($permissions as $permission) 
    {
        Auth::user()->checkPermission($permission);
    }
});

/*
|--------------------------------------------------------------------------
| Ajax Filters
|--------------------------------------------------------------------------
|
| The following filter is used allow ajax only requests 
|
*/

Route::filter('cmscanvas.ajax', function()
{
    if ( ! \Request::ajax())
    {
        App::abort(404);
    }
});
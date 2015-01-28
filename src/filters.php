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
| Cache Filters
|--------------------------------------------------------------------------
|
| The following filter is used to flush the cache directory
|
*/

Route::filter('cmscanvas.permission', function($route, $request)
{
    $actions = $route->getAction();
    $permissions = (isset($actions['permission'])) ? (array) $actions['permission'] : array();

    foreach ($permissions as $permission) 
    {
        if (!Auth::user()->can($permission))
        {
            App::abort(
                403, 
                "You do not have permission to access this page, please refer to your system administrator."
                . " (Permission: $permission)"
            );
        }
    }
});
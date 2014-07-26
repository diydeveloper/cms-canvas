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


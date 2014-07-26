<?php

use \App;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Models\Language;
use CmsCanvas\Models\Route\ModelBindings;

list($defaultLocale, $locales, $contentTypes, $entries, $modelBindings) = Cache::rememberForever('cmscanvas.routes', function()
{
    $languages = Language::where('active', 1)
        ->get();

    $defaultLocale = $languages->getFirstWhere('default', 1)
        ->locale;

    $locales = $languages->getWhere('default', 0)
        ->getKeyValueArray('id', 'locale');

    $contentTypes = Type::whereNotNull('route')
        ->get();

    $entries = Entry::with('contentType')
        ->whereNotNull('route')
        ->get();

    $modelBindings = ModelBindings::all();

    return array($defaultLocale, $locales, $contentTypes, $entries, $modelBindings);
});

foreach ($modelBindings as $modelBinding)
{
    Route::model($modelBinding->parameter, $modelBinding->model);
}

$firstSegment = Request::segment(1);
$locale = null;

Lang::setFallback($defaultLocale);

if (in_array($firstSegment, $locales))
{
    $locale = $firstSegment;
    Lang::setLocale($locale);
}
    
Route::group(array('prefix' => $locale), function() use($contentTypes, $entries)
{
    foreach($contentTypes as $contentType)
    {
        Route::any(
            $contentType->getRoute(), 
            array(
                'as' => $contentType->getRouteName(), 
                'uses' => 'CmsCanvas\Controllers\PageController@showPage'
            )
        );
    }

    foreach($entries as $entry)
    {
        Route::any(
            $entry->getRoute(), 
            array(
                'as' => $entry->getRouteName(), 
                'uses' => 'CmsCanvas\Controllers\PageController@showPage'
            )
        );
    }

});

App::missing(function($exception)
{
    return App::make('\CmsCanvas\Controllers\PageController')->callAction('show404Page', array($exception));
});


<?php

use \App;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Models\Language;
use CmsCanvas\Models\Route\ModelBindings;

list($defaultLocale, $locales, $contentTypes, $entries) = Cache::rememberForever('cmscanvas.routes', function()
{
    $languages = Language::where('active', 1)
        ->get();

    $defaultLocale = $languages->getFirstWhere('default', 1)
        ->locale;

    $locales = $languages->getWhere('default', 0)
        ->lists('locale', 'id');

    $contentTypes = Type::whereNotNull('route')
        ->get();

    $entries = Entry::with('contentType')
        ->whereNotNull('route')
        ->get();

    return array($defaultLocale, $locales, $contentTypes, $entries);
});

$firstSegment = Request::segment(1);
$locale = null;

Lang::setFallback($defaultLocale);

if (in_array($firstSegment, $locales))
{
    $locale = $firstSegment;
    Lang::setLocale($locale);
}
    
Route::group(['prefix' => $locale], function() use($contentTypes, $entries)
{
    foreach ($contentTypes as $contentType)
    {
        Route::any(
            $contentType->getRoute(), 
            array(
                'as' => $contentType->getRouteName(), 
                'uses' => 'CmsCanvas\Http\Controllers\PageController@showPage'
            )
        );
    }

    foreach ($entries as $entry)
    {
        Route::any(
            $entry->getRoute(), 
            array(
                'as' => $entry->getRouteName(), 
                'uses' => 'CmsCanvas\Http\Controllers\PageController@showPage'
            )
        );
    }

    $homeEntryId = \Config::get('cmscanvas::config.site_homepage');
    Route::any(
        '/', 
        [
            'as' => 'entry.'.$homeEntryId.'.'.\Lang::getLocale(), 
            'uses' => 'CmsCanvas\Http\Controllers\PageController@showPage'
        ]
    );

});


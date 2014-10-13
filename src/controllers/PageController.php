<?php namespace CmsCanvas\Controllers;

use Theme, Route, Cache, Config, stdClass, Content;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Container\Cache\Page;

class PageController extends PublicController {

    public function showPage()
    {
        $routeName = Route::currentRouteName();
        $parameters = Route::current()->parameters();
        $routeArray = explode('.', $routeName);

        list($objectType, $objectId) = $routeArray;

        $cache = Cache::rememberForever($routeName, function() use($objectType, $objectId)
        {
            return new Page($objectId, $objectType);
        });

        $content = $cache->render($parameters);
        $this->layout->content = $content;

        print_pre(\DB::getQueryLog());
    }

    public function show404Page($exception)
    {
        $entryId = Config::get('cmscanvas::config.custom_404');

        $entry = Entry::find($entryId);
        $content = $entry->render();

        $this->layout->content = $content;
    }

}

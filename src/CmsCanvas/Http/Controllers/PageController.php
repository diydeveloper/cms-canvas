<?php namespace CmsCanvas\Http\Controllers;

use Theme, Route, Cache, Config, stdClass, Content, Lang;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Container\Cache\Page;
use CmsCanvas\Http\Controllers\PublicController;

class PageController extends PublicController {

    public function showPage($exception = null)
    {
        $routeName = Route::currentRouteName();

        if ($routeName == null)
        {
            // Route not found. Show 404 page.
            $entryId = Config::get('cmscanvas::config.custom_404');
            $routeName = 'entry.'.$entryId.'.'.Lang::getLocale();
            $parameters = array();
        }
        else
        {
            $parameters = Route::current()->parameters();
        }

        $routeArray = explode('.', $routeName);

        list($objectType, $objectId) = $routeArray;

        $cache = Cache::rememberForever($routeName, function() use($objectType, $objectId)
        {
            return new Page($objectId, $objectType);
        });

        $content = $cache->render($parameters);
        $this->layout->content = $content;
    }

}

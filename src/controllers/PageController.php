<?php namespace CmsCanvas\Controllers;

use Theme, Route, Cache, Config;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;

class PageController extends PublicController {

    public function showPage()
    {
        $routeName = Route::currentRouteName();
        $parameters = Route::current()->parameters();
        $routeArray = explode('.', $routeName);

        list($objectType, $objectId) = $routeArray;

        $content = Cache::rememberForever($routeName, function() use($objectType, $objectId)
        {
            if ($objectType == 'contentType')
            {
                $object = Type::find($objectId);
            }
            else
            {
                $object = Entry::find($objectId);
            }

            return $object->render();
        });

        $content->mergeData($parameters);
        $this->layout->content = $content;

        print_pre(\DB::getQueryLog());
    }

    public function show404Page($exception)
    {
        $entryId = Config::get('cmscanvas::config.custom_404');

        $entry = Entry::find($entryId);
        $content = $entry->cacheRender();

        $this->layout->content = $content;
    }

}

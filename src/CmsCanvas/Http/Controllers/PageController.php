<?php 

namespace CmsCanvas\Http\Controllers;

use Route, Cache, Config, stdClass, Content, Lang;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Container\Cache\Page;
use CmsCanvas\Http\Controllers\PublicController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends PublicController {

    public function showPage($exception = null)
    {
        $routeName = Route::currentRouteName();

        if ($routeName == null || $exception instanceof NotFoundHttpException) {
            // Route not found. Show 404 page.
            $entryId = Config::get('cmscanvas::config.custom_404');
            $routeName = 'entry.'.$entryId.'.'.Lang::getLocale();
            $parameters = [];
        } else {
            $parameters = Route::current()->parameters();
        }

        $routeArray = explode('.', $routeName);

        list($objectType, $objectId, $locale) = $routeArray;

        $cacheKey = $objectType.'.'.$objectId.'.'.$locale;

        $cache = Cache::rememberForever($cacheKey, function() use($objectType, $objectId) {
            return new Page($objectId, $objectType);
        });

        return $cache->render($parameters);
    }

}

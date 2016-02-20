<?php 

namespace CmsCanvas\Http\Controllers;

use Twig;
use Route, Cache, Config, Lang, Theme;
use CmsCanvas\Container\Cache\Page;
use CmsCanvas\Http\Controllers\PublicController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

class PageController extends PublicController {

    public function showPage(Request $request, $exception = null)
    {
        $routeName = Route::currentRouteName();

        if ($routeName == null || $exception instanceof NotFoundHttpException) {
            // Route not found. Show 404 page.
            $entryId = Config::get('cmscanvas::config.custom_404');
            $routeName = 'entry.'.$entryId.'.'.Lang::getLocale();
        }

        $routeArray = explode('.', $routeName);

        list($objectType, $objectId, $locale) = $routeArray;

        $cacheKey = $objectType.'.'.$objectId.'.'.$locale;

        $cache = Cache::rememberForever($cacheKey, function() use($objectType, $objectId) {
            return new Page($objectId, $objectType);
        });

        if (Theme::includeAdminToolbar($cache->getResource())) {
            if ($request->input('admin_toggle_inline_editing')) {
                $user = auth()->user();
                $user->enable_inline_editing = !$user->enable_inline_editing;
                $user->save();
                return redirect()->back();
            }
        }

        return $cache->setThemeMetadata()->render();
    }

}

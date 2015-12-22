<?php

namespace CmsCanvas\Providers;

use Config, Lang, Request, Cache;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;
use CmsCanvas\Models\Language;
use CmsCanvas\Models\Route\ModelBindings;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'CmsCanvas\Http\Controllers';

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @var Collection
     */
    protected $locales;

    /**
     * @var Collection
     */
    protected $entries;

    /**
     * @var Collection
     */
    protected $contentTypes;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        //

        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map(Router $router)
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $router->group(['namespace' => $this->namespace], function ($router) {
            if ($this->app['admin']->getUrlPrefix() == Request::segment(1)) {
                require __DIR__.'/../Http/adminRoutes.php';
            } else {
                $this->prepareRouteData();
                $this->setLocale();
                $this->mapContentTypes($router);
                $this->mapEntries($router);
                $this->mapHomePage($router);
            }
        });
    }

    /**
     * Return the requested locale.
     *
     * @return void
     */
    protected function setLocale()
    {
        $locale = null;
        $urlArray = explode('.', parse_url(Request::url(), PHP_URL_HOST));
        $subdomain = $urlArray[0];

        Lang::setFallback($this->defaultLocale);

        if (in_array($subdomain, $this->locales->all())) {
            $locale = $subdomain;
            Lang::setLocale($locale);
        }
    }

    /**
     * Define content type routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapContentTypes(Router $router)
    {
        foreach ($this->contentTypes as $contentType) {
            $router->any(
                $contentType->getRoute(), 
                [
                    'as' => $contentType->getRouteName(), 
                    'uses' => 'PageController@showPage'
                ]
            );
        }
    }

    /**
     * Define entry routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapEntries(Router $router)
    {
        foreach ($this->entries as $entry) {
            if ($entry->getRoute() !== null) {
                $router->any(
                    $entry->getRoute(), 
                    [
                        'as' => $entry->getRouteName(), 
                        'uses' => 'PageController@showPage'
                    ]
                );
            }

            if ($entry->getDynamicRoute() !== null) {
                $router->any(
                    $entry->getDynamicRoute(), 
                    [
                        'as' => $entry->getDynamicRouteName(), 
                        'uses' => 'PageController@showPage'
                    ]
                );
            }
        }
    }

    /**
     * Define the home page entry route for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function mapHomePage(Router $router)
    {
        $homeEntryId = Config::get('cmscanvas::config.site_homepage');
        $router->any(
            '/', 
            [
                'as' => 'entry.'.$homeEntryId.'.'.Lang::getLocale(), 
                'uses' => 'PageController@showPage'
            ]
        );
    }

    /**
     * Populates class variables with route data
     *
     * @return void
     */
    protected function prepareRouteData()
    {
        $data = Cache::rememberForever('cmscanvas.routes', function() {
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
                ->orWhereHas('contentType', function($query) {
                    $query->whereNotNull('entry_uri_template');
                })
                ->get();

            return [$defaultLocale, $locales, $contentTypes, $entries];
        });

        $this->defaultLocale = $data[0];
        $this->locales = $data[1];
        $this->contentTypes = $data[2];
        $this->entries = $data[3];
    }
}

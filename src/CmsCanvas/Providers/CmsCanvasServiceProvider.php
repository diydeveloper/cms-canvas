<?php 

namespace CmsCanvas\Providers;

use App, Event, DateTime, View, Request;
use Illuminate\Support\ServiceProvider;
use CmsCanvas\Theme\ThemePublisher;
use CmsCanvas\Commands\ThemePublishCommand;
use CmsCanvas\Theme\Theme;
use CmsCanvas\Admin\Admin;
use CmsCanvas\Content\Content;

class CmsCanvasServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        \DB::enableQueryLog();

        $this->setupConfig();
        $this->setupViews();
        $this->setupMiddleware();
        $this->setupPublishing();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    public function setupConfig()
    {
        $source = realpath(__DIR__.'/../../config/config.php');
        $this->mergeConfigFrom($source, 'cmscanvas::config');

        $source = realpath(__DIR__.'/../../config/admin.php');
        $this->mergeConfigFrom($source, 'cmscanvas::admin');

        $source = realpath(__DIR__.'/../../config/assets.php');
        $this->mergeConfigFrom($source, 'cmscanvas::assets');
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    public function setupViews()
    {
        $source = realpath(__DIR__.'/../../resources/views/');
        $this->loadViewsFrom($source, 'cmscanvas');
    }

    /**
     * Include filter files.
     *
     * @return void
     */
    public function setupMiddleware()
    {
        $this->app->router->middleware('cmscanvas.auth', 'CmsCanvas\Http\Middleware\Authenticate');
        $this->app->router->middleware('cmscanvas.permission', 'CmsCanvas\Http\Middleware\Permission');
        $this->app->router->middleware('cmscanvas.ajax', 'CmsCanvas\Http\Middleware\Ajax');
        $this->app->router->middleware('cmscanvas.flushCache', 'CmsCanvas\Http\Middleware\FlushCache');
    }

    /**
     * Defines file groups to be published
     *
     * @return void
     */
    public function setupPublishing()
    {
        $this->publishes(
            [__DIR__.'/../../resources/themes/default' => base_path('/resources/themes/default')],
            'themes'
        );

        $this->publishes(
            [__DIR__.'/../../database/migrations/' => database_path('migrations')],
            'migrations'
        );

        $this->publishes(
            [__DIR__.'/../../../public/' => public_path('diyphpdeveloper/cmscanvas'),],
            'public'
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTheme();
        $this->registerAuthLoginEventListener();
        $this->registerDisplayError();
        $this->registerAdmin();
        $this->registerContent();
    }

    /**
     * Register theme provider.
     *
     * @return void
     */
    protected function registerTheme()
    {
        $this->app->alias('theme', 'CmsCanvas\Theme\Theme');
        $this->app->singleton('theme', function($app) {
            return new Theme;
        });
    }

    /**
     * Register admin provider.
     *
     * @return void
     */
    protected function registerAdmin()
    {
        $this->app->bind('admin', function($app) {
            return new Admin;
        });
    }

    /**
     * Register content provider.
     *
     * @return void
     */
    protected function registerContent()
    {
        $this->app->bind('content', function() {
            return new Content;
        });
    }

    /**
     * Register app error view.
     *
     * @return void
     */
    protected function registerDisplayError()
    {
        $this->app->singleton(
            'Illuminate\Contracts\Debug\ExceptionHandler',
            'CmsCanvas\Exceptions\Handler'
        );
    }

    /**
     * Register auth login event listener
     *
     * @return void
     */
    protected function registerAuthLoginEventListener()
    {
        Event::listen('auth.login', function($user) {
            $user->last_login = new DateTime;
            $user->save();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'theme',
            'admin',
            'content',
        ];
    }

}

<?php namespace Cmscanvas;

use App, Event, DateTime, View, Request, Admin, Theme;
use Illuminate\Support\ServiceProvider;

class CmscanvasServiceProvider extends ServiceProvider {

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
        $this->package('diyphpdeveloper/cmscanvas', 'cmscanvas', __DIR__.'/../');

        // Include the content tyep field views
        View::addNamespace('CmsCanvas\Content\Type\FieldType', __DIR__.'/Content/Type/FieldType/views/');

        if (Admin::getUrlPrefix() == Request::segment(1))
        {
            include __DIR__.'/../adminRoutes.php';
        }
        else
        {
            include __DIR__.'/../routes.php';
        }
        
        include __DIR__.'/../filters.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        App::error(function(\Exception $exception, $code)
        {
            Theme::setTheme('admin');
            Theme::setLayout('layouts.default');
            Theme::addPackage(array('jquery', 'jquerytools', 'admin_jqueryui'));
            $layout = Theme::getLayout();

            $heading = 'Permission Denied';
            $layout->content = View::make('cmscanvas::admin.error')
                ->with(
                    array(
                        'heading' => $heading,
                        'exception' => $exception
                    )
                );

            return \Response::make($layout, $code);
        });

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
        return array();
    }

}

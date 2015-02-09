<?php namespace Cmscanvas;

use App, Event, DateTime, View, Request;
use Illuminate\Support\ServiceProvider;
use CmsCanvas\Theme\ThemePublisher;
use CmsCanvas\Commands\ThemePublishCommand;
use CmsCanvas\Theme\Theme;
use CmsCanvas\Admin\Admin;
use CmsCanvas\Content\Content;
use CmsCanvas\StringView\StringView;
use CmsCanvas\Exception\Exception;

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

        if ($this->app['admin']->getUrlPrefix() == Request::segment(1))
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
        $this->registerThemePublisher();
        $this->registerTheme();
        $this->registerAuthLoginEventListener();
        $this->registerDisplayError();
        $this->registerAdmin();
        $this->registerContent();
        $this->registerStringView();
    }

    /**
     * Register theme provider.
     *
     * @return void
     */
    protected function registerTheme()
    {
        $this->app->bindShared('theme', function($app)
        {
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
        $this->app->bind('admin', function($app)
        {
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
        $this->app->bind('content', function()
        {
            return new Content;
        });
    }

    /**
     * Register string view provider.
     *
     * @return void
     */
    protected function registerStringView()
    {
        $this->app->bind('stringview', function()
        {
            return new StringView;
        });

        $this->app['stringview']->extend(function($value) {
            return preg_replace('/\@define(.+)/', '<?php ${1}; ?>', $value);
        });

        $this->app['stringview']->extend(function($value, $compiler) {
            return preg_replace('/\@entries\s*\(\'?([a-zA-Z0-9_]+)\'?\s*,\s*(.+)\)/', '<?php $${1} = Content::entries(${2}); ?>', $value);
        });
    }

    /**
     * Register the theme publisher class and command.
     *
     * @return void
     */
    protected function registerThemePublisher()
    {
        $this->registerThemePublishCommand();

        $this->app->bindShared('theme.publisher', function($app)
        {
            $viewPath = $app['path'].'/themes';

            // Once we have created the view publisher, we will set the default packages
            // path on this object so that it knows where to find all of the packages
            // that are installed for the application and can move them to the app.
            $publisher = new ThemePublisher($app['files'], $viewPath);

            $publisher->setPackagePath($app['path.base'].'/vendor');

            return $publisher;
        });

        $this->commands('command.theme.publish');
    }

    /**
     * Register the view publish console command.
     *
     * @return void
     */
    protected function registerThemePublishCommand()
    {
        $this->app->bindShared('command.theme.publish', function($app)
        {
            return new ThemePublishCommand($app['theme.publisher']);
        });
    }

    /**
     * Register app error view.
     *
     * @return void
     */
    protected function registerDisplayError()
    {
        App::error(function(\CmsCanvas\Exception\ExceptionDisplayInterface $exception, $code)
        {
            $view = $exception->getView();

            return \Response::make($view, $code);
        });    
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
        return array(
            'theme.publisher',
            'command.theme.publish',
            'theme',
            'admin',
            'content',
        );
    }

}

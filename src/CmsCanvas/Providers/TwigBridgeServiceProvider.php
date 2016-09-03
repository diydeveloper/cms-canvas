<?php

namespace CmsCanvas\Providers;

use TwigBridge\ServiceProvider;
use CmsCanvas\TwigBridge\StringView\Factory as StringViewFactory;

class TwigBridgeServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        parent::register();

        $this->registerStringView();
        $this->registerTimezone();
    }

    /**
     * Register string view bindings.
     *
     * @return void
     */
    public function registerStringView()
    {
        $this->app->bind('stringview', function ($app) {
            return new StringViewFactory($app);
        });
    }

    /**
     * Register default timezone
     *
     * @return void
     */
    public function registerTimezone()
    {
        $defaultTimezone = $this->app['config']->get('cmscanvas::config.default_timezone');
        if ($defaultTimezone != null) {
            $this->app['twig']->getExtension('core')->setTimezone($defaultTimezone);
        }
    }

    /**
     * Load the configuration files and allow them to be published.
     *
     * @return void
     */
    protected function loadConfiguration()
    {
        $configPath = __DIR__ . '/../../config/twigbridge.php';

        $this->mergeConfigFrom($configPath, 'twigbridge');
    }
}
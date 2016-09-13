<?php

namespace CmsCanvas\Providers;

use TwigBridge\ServiceProvider;
use TwigBridge\Engine\Twig;
use TwigBridge\Engine\Compiler;
use TwigBridge\Bridge;
use CmsCanvas\TwigBridge\StringView\Factory as StringViewFactory;
use InvalidArgumentException;


class TwigBridgeServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        parent::register();
        $this->registerStringView();
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
     * Register Twig engine bindings.
     *
     * @return void
     */
    protected function registerEngine()
    {
        $this->app->bindIf(
            'twig',
            function () {
                $extensions = $this->app['twig.extensions'];
                $lexer      = $this->app['twig.lexer'];
                $twig       = new Bridge(
                    $this->app['twig.loader'],
                    $this->app['twig.options'],
                    $this->app
                );

                // Instantiate and add extensions
                foreach ($extensions as $extension) {
                    // Get an instance of the extension
                    // Support for string, closure and an object
                    if (is_string($extension)) {
                        try {
                            $extension = $this->app->make($extension);
                        } catch (\Exception $e) {
                            throw new InvalidArgumentException(
                                "Cannot instantiate Twig extension '$extension': " . $e->getMessage()
                            );
                        }
                    } elseif (is_callable($extension)) {
                        $extension = $extension($this->app, $twig);
                    } elseif (!is_a($extension, 'Twig_Extension')) {
                        throw new InvalidArgumentException('Incorrect extension type');
                    }

                    $twig->addExtension($extension);
                }

                // Set lexer
                if (is_a($lexer, 'Twig_LexerInterface')) {
                    $twig->setLexer($lexer);
                }

                $defaultTimezone = config('cmscanvas.config.default_timezone');
                if ($defaultTimezone != null) {
                    $twig->getExtension('core')->setTimezone($defaultTimezone);
                }

                return $twig;
            },
            true
        );

        $this->app->alias('twig', 'Twig_Environment');
        $this->app->alias('twig', 'TwigBridge\Bridge');

        $this->app->bindIf('twig.compiler', function () {
            return new Compiler($this->app['twig']);
        });

        $this->app->bindIf('twig.engine', function () {
            return new Twig(
                $this->app['twig.compiler'],
                $this->app['twig.loader.viewfinder'],
                $this->app['config']->get('twigbridge.twig.globals', [])
            );
        });
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
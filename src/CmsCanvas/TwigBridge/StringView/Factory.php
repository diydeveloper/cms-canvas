<?php

namespace CmsCanvas\TwigBridge\StringView;

use CmsCanvas\TwigBridge\StringView\StringView;
use Illuminate\Contracts\View\Factory as FactoryContract;
use CmsCanvas\TwigBridge\StringView\Loader;
use Illuminate\Foundation\Application;

class Factory implements FactoryContract
{

    /**
     * Twig environment
     *
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * Construct
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->twig = $app['twig'];
    }

    /**
     * Determine if a given view exists.
     *
     * @param  string  $view
     * @return bool
     */
    public function exists($view) {}

    /**
     * Get the evaluated view contents for the given path.
     *
     * @param  string  $path
     * @param  array  $data
     * @param  array  $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    public function file($path, $data = [], $mergeData = []) {}

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    public function make($view, $data = [], $mergeData = [])
    {
        $data = array_merge($mergeData, $data);

        return new StringView($this, $view, $data);
    }

    /**
     * Add a piece of shared data to the environment.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function share($key, $value = null)
    {
        $this->twig->addGlobal($key, $value);
    }

   /**
     * Register a view composer event.
     *
     * @param  array|string  $views
     * @param  \Closure|string  $callback
     * @param  int|null  $priority
     * @return array
     */ 
    public function composer($views, $callback, $priority = null) {}

    /**
     * Register a view creator event.
     *
     * @param  array|string  $views
     * @param  \Closure|string  $callback
     * @return array
     */
    public function creator($views, $callback) {}

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function addNamespace($namespace, $hints) {}

    /**
     * Replace the namespace hints for the given namespace.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return $this
     */
    public function replaceNamespace($namespace, $hints) {}

    /**
     * Add a new namespace to the loader.
     *
     * @param  \CmsCanvas\TwigBridge\StringView\StringView  $view
     * @return \TwigBridge\Twig\Template
     */
    public function resolveTemplate(StringView $view)
    {
        $currentLoader = $this->twig->getLoader();

        $loader = new Loader($view);
        $this->twig->setLoader($loader);

        $template = $this->twig->resolveTemplate($view->getName());

        $this->twig->setLoader($currentLoader);

        return $template;
    }

}

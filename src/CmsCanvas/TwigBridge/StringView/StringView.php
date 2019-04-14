<?php 

namespace CmsCanvas\TwigBridge\StringView;

use CmsCanvas\TwigBridge\StringView\Loader;
use CmsCanvas\TwigBridge\StringView\Factory;
use Illuminate\Contracts\View\View as ViewContract;
use InvalidArgumentException;

class StringView implements ViewContract {

    /**
     * @var \TwigBridge\StringView\Factory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $view;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * @var string
     */
    protected $updatedAt;

    /**
     * @var \TwigBridge\Twig\Template
     */
    protected $template;

    /**
     * @var string
     */
    protected $render;

    /** 
     * Constructor for StringView
     *
     * @param  \CmsCanvas\TwigBridge\StringView\Factory  $factory
     * @param  string $view
     * @param  array  $data
     * @return void
     */
    public function __construct(Factory $factory, $view, $data = [])
    {
        $this->factory = $factory;
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function getName()
    {
        return $this->cacheKey;
    }

    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function name()
    {
        return $this->getName();
    }

    /**
     * Get the cache key for the view.
     *
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * Get the last modified timestamp for the view.
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets the key used to cache the view
     *
     * @param  string  $cacheKey
     * @return $this
     */
    public function cacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;

        $this->prepare();

        return $this;
    }

    /**
     * Sets the timestamp the view was last modified
     *
     * @param  string  $cacheKey
     * @return $this
     */
    public function updatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        $this->prepare();

        return $this;
    }

    /**
     * Add a piece of data to the view.
     *
     * @param  string|array  $key
     * @param  mixed   $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Resolves the template if both the cacheKey and updatedAt have been set
     *
     * @return void
     */
    protected function prepare()
    {
        if ($this->cacheKey === null || $this->updatedAt === null) {
            return;
        }

        $this->template = $this->factory->resolveTemplate($this);
    }

    /**
     * Render the template prior to being casted to a string
     *
     * @return $this
     */
    public function prerender()
    {
        try {
            $this->render();
        } catch(\Exception $e) {
            $previousException = $e->getPrevious();
            if ($previousException instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                throw $previousException;
            }
        }

        return $this;
    }

    /**
     * Renders the view to a string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $previous = $e->getPrevious();

            if ($previous != null) {
                $message .= ' :: '.get_class($previous).' :: '.$previous->getMessage();
            }

            return $message;
        }
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render()
    {
        if ($this->cacheKey === null) {
            throw new InvalidArgumentException('A cache key must be specified');
        }

        if ($this->updatedAt === null) {
            throw new InvalidArgumentException('A updated at timestamp must be specified');
        }

        if ($this->template == null) {
            throw new InvalidArgumentException('A valid template does not exist.');
        }

        if ($this->render == null) {
            $this->render = $this->template->render($this->data);
        }

        return $this->render;
    }

    /**
     * Returns the source for the current view
     *
     * @return string
     */
    public function getSource()
    {
        return $this->view; 
    }

    /**
     * Get the array of view data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

}

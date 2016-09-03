<?php

namespace CmsCanvas\TwigBridge\StringView;

use Twig_LoaderInterface;
use Twig_Error_Loader;
use Twig_ExistsLoaderInterface;
use InvalidArgumentException;
use CmsCanvas\TwigBridge\StringView\StringView;

/**
 * Basic loader using absolute paths.
 */
class Loader implements Twig_LoaderInterface, Twig_ExistsLoaderInterface
{
    protected $templates = [];

    /**
     * Constructor.
     *
     * @param array $templates An array of templates (keys are the names, and values are the source code)
     */
    public function __construct(StringView $view)
    {
        $this->templates[$view->getName()] = $view;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return isset($this->templates[(string) $name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        $name = (string) $name;
        if (!isset($this->templates[$name])) {
            throw new Twig_Error_Loader(sprintf('Template "%s" is not defined.', $name));
        }

        return $this->templates[$name]->getSource();
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        $name = (string) $name;
        if (!isset($this->templates[$name])) {
            throw new Twig_Error_Loader(sprintf('Template "%s" is not defined.', $name));
        }

        return $this->templates[$name]->getCacheKey();
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        $name = (string) $name;
        if (!isset($this->templates[$name])) {
            throw new Twig_Error_Loader(sprintf('Template "%s" is not defined.', $name));
        }

        return $this->templates[$name]->getUpdatedAt() <= $time;
    }
}

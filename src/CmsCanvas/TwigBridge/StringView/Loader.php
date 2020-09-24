<?php

namespace CmsCanvas\TwigBridge\StringView;

use Twig\Loader\LoaderInterface;
use Twig\Error\LoaderError;
use Twig\Source;
use InvalidArgumentException;
use CmsCanvas\TwigBridge\StringView\StringView;

/**
 * Basic loader using absolute paths.
 */
class Loader implements LoaderInterface
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
    public function getSourceContext($name)
    {
        $name = (string) $name;
        if (!isset($this->templates[$name])) {
            throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
        }

        return new Source($this->templates[$name]->getSource(), $name, '');
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        $name = (string) $name;
        if (!isset($this->templates[$name])) {
            throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
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
            throw new LoaderError(sprintf('Template "%s" is not defined.', $name));
        }

        return $this->templates[$name]->getUpdatedAt() <= $time;
    }
}

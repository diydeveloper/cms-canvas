<?php

namespace CmsCanvas\TwigBridge\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Twig_SimpleTest;
use CmsCanvas\Content\Content as ContentManager;

/**
 * Access CmsCanvas's theme class in your Twig templates.
 */
class Content extends Twig_Extension
{
    /**
     * @var \CmsCanvas\Content\Content
     */
    protected $content;

    /**
     * Create a new config extension
     *
     * @param \CmsCanvas\Content\Content
     */
    public function __construct(ContentManager $content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'CmsCanvas_TwigBridge_Extension_Content';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('entries', [$this->content, 'entries'], ['is_variadic' => true]),
            new Twig_SimpleFunction('entry_first', [$this->content, 'entryFirst'], ['is_variadic' => true]),
            new Twig_SimpleFunction('entry', [$this->content, 'entry']),
            new Twig_SimpleFunction('navigation', [$this->content, 'navigation'], ['is_variadic' => true]),
            new Twig_SimpleFunction('breadcrumb', [$this->content, 'breadcrumb'], ['is_variadic' => true]),
            new Twig_SimpleFunction('is_home_page', [$this->content, 'isHomePage']),
            new Twig_SimpleFunction('thumbnail', [$this->content, 'thumbnail'], ['is_variadic' => true]),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('user_date', [$this->content, 'userDate']),
            new Twig_SimpleFilter('thumbnail', [$this->content, 'thumbnail'], ['is_variadic' => true]),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getTests()
    {
        return [
            new Twig_SimpleTest('entry', function($value) {
                return ($value instanceof \CmsCanvas\Content\Entry\Render);
            }),
            new Twig_SimpleTest('content_type', function($value) {
                return ($value instanceof \CmsCanvas\Content\Type\Render);
            }),
        ];
    }
}

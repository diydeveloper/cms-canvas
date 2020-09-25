<?php

namespace CmsCanvas\TwigBridge\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Twig\TwigTest;
use CmsCanvas\Content\Content as ContentManager;

/**
 * Access CmsCanvas's theme class in your Twig templates.
 */
class Content extends AbstractExtension
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
            new TwigFunction('entries', [$this->content, 'entries'], ['is_variadic' => true]),
            new TwigFunction('entry_first', [$this->content, 'entryFirst'], ['is_variadic' => true]),
            new TwigFunction('entry', [$this->content, 'entry']),
            new TwigFunction('navigation', [$this->content, 'navigation'], ['is_variadic' => true]),
            new TwigFunction('breadcrumb', [$this->content, 'breadcrumb'], ['is_variadic' => true]),
            new TwigFunction('is_home_page', [$this->content, 'isHomePage']),
            new TwigFunction('thumbnail', [$this->content, 'thumbnail'], ['is_variadic' => true]),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('user_date', [$this->content, 'userDate']),
            new TwigFilter('thumbnail', [$this->content, 'thumbnail'], ['is_variadic' => true]),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getTests()
    {
        return [
            new TwigTest('entry', function($value) {
                return ($value instanceof \CmsCanvas\Content\Entry\Render);
            }),
            new TwigTest('content_type', function($value) {
                return ($value instanceof \CmsCanvas\Content\Type\Render);
            }),
        ];
    }
}

<?php

namespace CmsCanvas\TwigBridge\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
use CmsCanvas\Theme\Theme as ThemeManager;

/**
 * Access CmsCanvas's theme class in your Twig templates.
 */
class Theme extends Twig_Extension
{
    /**
     * @var \CmsCanvas\Theme\Theme
     */
    protected $theme;

    /**
     * Create a new config extension
     *
     * @param \CmsCanvas\Theme\Theme
     */
    public function __construct(ThemeManager $theme)
    {
        $this->theme = $theme;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'CmsCanvas_TwigBridge_Extension_Theme';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('theme_head', [$this->theme, 'head']),
            new Twig_SimpleFunction('theme_footer', [$this->theme, 'footer']),
            new Twig_SimpleFunction('theme_asset', [$this->theme, 'asset']),
            new Twig_SimpleFunction('theme_metadata', [$this->theme, 'metadata']),
            new Twig_SimpleFunction('theme_javascripts', [$this->theme, 'javascripts']),
            new Twig_SimpleFunction('theme_stylesheets', [$this->theme, 'stylesheets']),
            new Twig_SimpleFunction('theme_analytics', [$this->theme, 'analytics']),
            new Twig_SimpleFunction('theme_meta_title', [$this->theme, 'metaTitle']),
            new Twig_SimpleFunction('theme_meta_description', [$this->theme, 'metaDescription']),
            new Twig_SimpleFunction('theme_meta_keywords', [$this->theme, 'metaKeywords']),
        ];
    }
}

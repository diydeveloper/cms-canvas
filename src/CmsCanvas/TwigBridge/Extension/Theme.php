<?php

namespace CmsCanvas\TwigBridge\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use CmsCanvas\Theme\Theme as ThemeManager;

/**
 * Access CmsCanvas's theme class in your Twig templates.
 */
class Theme extends AbstractExtension
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
            new TwigFunction('theme_head', [$this->theme, 'head']),
            new TwigFunction('theme_footer', [$this->theme, 'footer']),
            new TwigFunction('theme_asset', [$this->theme, 'asset']),
            new TwigFunction('theme_metadata', [$this->theme, 'metadata']),
            new TwigFunction('theme_javascripts', [$this->theme, 'javascripts']),
            new TwigFunction('theme_stylesheets', [$this->theme, 'stylesheets']),
            new TwigFunction('theme_analytics', [$this->theme, 'analytics']),
            new TwigFunction('theme_meta_title', [$this->theme, 'metaTitle']),
            new TwigFunction('theme_meta_description', [$this->theme, 'metaDescription']),
            new TwigFunction('theme_meta_keywords', [$this->theme, 'metaKeywords']),
        ];
    }
}

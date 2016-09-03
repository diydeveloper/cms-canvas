<?php

namespace CmsCanvas\TwigBridge\Extension;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use Illuminate\Translation\Translator as LaravelTranslator;

/**
 * Access Laravels translator class in your Twig templates.
 */
class Translator extends Twig_Extension
{
    /**
     * @var \Illuminate\Translation\Translator
     */
    protected $translator;

    /**
     * Create a new translator extension
     *
     * @param \Illuminate\Translation\Translator
     */
    public function __construct(LaravelTranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'CmsCanvas_TwigBridge_Extension_Translator';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('trans_locale', [$this->translator, 'getLocale']),
        ];
    }

}

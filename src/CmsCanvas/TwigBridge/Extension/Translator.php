<?php

namespace CmsCanvas\TwigBridge\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Illuminate\Translation\Translator as LaravelTranslator;

/**
 * Access Laravels translator class in your Twig templates.
 */
class Translator extends AbstractExtension
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
            new TwigFunction('trans_locale', [$this->translator, 'getLocale']),
        ];
    }

}

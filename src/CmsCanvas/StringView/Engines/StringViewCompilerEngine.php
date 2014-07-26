<?php namespace CmsCanvas\StringView\Engines;

use Illuminate\View\Engines\CompilerEngine;

class StringViewCompilerEngine extends CompilerEngine {

    /**
     * Get the exception message for an exception.
     *
     * @param  \Exception  $e
     * @return string
     */
    protected function getMessage($e)
    {
        return $e->getMessage().' (StringView: '.last($this->lastCompiled)->cache_key.')';
    }

}
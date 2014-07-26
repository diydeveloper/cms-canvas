<?php namespace CmsCanvas\Content\Facades;

use Illuminate\Support\Facades\Facade;

class Content extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
     protected static function getFacadeAccessor() { return 'content'; }

}
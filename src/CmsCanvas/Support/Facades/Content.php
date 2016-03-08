<?php 

namespace CmsCanvas\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Content extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() 
    { 
        return 'content'; 
    }

}
<?php 

namespace CmsCanvas\Support\Contracts\View;

interface Render {

    /**
     * Outputs the object as a string
     *
     * @return string
     */
    public function __toString();

    /**
     * Returns the resource type for the render
     *
     * @return string
     */
    public function getResourceType();

}

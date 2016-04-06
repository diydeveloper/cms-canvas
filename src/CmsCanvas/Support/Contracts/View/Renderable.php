<?php 

namespace CmsCanvas\Support\Contracts\View;

interface Renderable {

    /**
     * Get the evaluated contents of the object.
     *
     * @return mixed
     */
    public function render();

}

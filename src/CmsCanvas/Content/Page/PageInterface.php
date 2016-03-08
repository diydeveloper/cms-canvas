<?php 

namespace CmsCanvas\Content\Page;

interface PageInterface {

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();

    /**
     * Get content type fields with data
     *
     * @return \CmsCanvas\Models\Content\Type\Field|Collection
     */
    public function getContentTypeFields();

}

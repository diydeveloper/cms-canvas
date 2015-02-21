<?php namespace CmsCanvas\Content\Page;

interface PageInterface {

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();

    /**
     * Get an array of transalated data for the current object
     *
     * @return array
     */
    public function getRenderedData();

    /**
     * Get content type fields with data
     *
     * @return \CmsCanvas\Models\Content\Type\Field|Collection
     */
    public function getContentTypeFields();

}

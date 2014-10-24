<?php namespace CmsCanvas\Content\Page;

interface PageInterface {

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();

    /**
     * Get the contents of the page
     *
     * @param array $parameters
     * @return \CmsCanvas\StringView\StringView
     */
    public function renderContents($parameters = array());

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

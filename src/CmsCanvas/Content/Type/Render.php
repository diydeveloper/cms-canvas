<?php 

namespace CmsCanvas\Content\Type;

use CmsCanvas\Content\Type\Builder\Type as ContentTypeBuilder;
use CmsCanvas\Support\Contracts\View\Render as ViewRender;

class Render implements ViewRender {

    /**
     * The content type builder to render from
     *
     * @var \CmsCanvas\Content\Type\Builder\Type
     */
    protected $contentTypeBuilder;

    /**
     * Defines the order in which to sort.
     *
     * @param \CmsCanvas\Models\Content\Type $contentType
     * @param array $parameters
     * @return void
     */
    public function __construct(ContentTypeBuilder $contentTypeBuilder)
    {
        $this->contentTypeBuilder = $contentTypeBuilder;
    }

    /**
     * Magic method to retrive rendered data
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->entryBuilder->getData($name);
    }

    /**
     * Magic method to catch undefined methods
     *
     * @param  string $name
     * @param  array $arguments
     * @return void
     */
    public function __call($name, $arguments)
    {
        //
    }

    /**
     * Magic method to determine if the property isset
     *
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return true;
    }

    /**
     * Magic method to render the contentType as a string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return (string) $this->contentTypeBuilder->renderContents();
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Returns the full route for the content type
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->contentTypeBuilder->getContentType()->getRoute();
    }

    /**
     * Returns the resource type for this render
     *
     * @return string
     */
    public function getResourceType()
    {
        return 'CONTENT_TYPE';
    }

}
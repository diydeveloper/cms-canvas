<?php 

namespace CmsCanvas\Container\Cache;

use Theme;
use CmsCanvas\Content\Page\PageInterface;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;

class Page implements PageInterface  {

    /**
     * An Entry or Content Type
     *
     * @var mixed
     */
    protected $resource;

    /**
     * Collection of content type fields with entry data
     *
     * @var \CmsCanvas\Models\Content\Type\Field|Collection
     */
    protected $contentTypeFields;

    /**
     * Defines the order in which to sort.
     *
     * @param  int $resourceId
     * @param  string $resourceType
     * @return void
     */
    public function __construct($resourceId, $resourceType = 'entry')
    {
        if ($resourceType == 'contentType') {
            $this->resource = Type::find($resourceId);
        } else {
            $this->resource = Entry::find($resourceId);
            $this->resource->contentType;
        }

        $this->contentTypeFields = $this->resource->getContentTypeFields(true);
    }

    /**
     * Renders the cached page
     *
     * @param array $parameters
     * @return \CmsCanvas\Content\Entry\Render|\CmsCanvas\Content\Type\Render
     */
    public function render($parameters = [])
    {
        $content = $this->resource
            ->setCache($this)
            ->render($parameters);

        $layoutName = $content->getThemeLayout();

        if ($layoutName != null) {
            Theme::setLayout($layoutName);
            $layout = Theme::getLayout();
            $layout->content = $content;

            return $layout;
        }

        return $content;
    }

    /**
     * Get content type fields with data
     *
     * @return \CmsCanvas\Models\Content\Type\Field|Collection
     */
    public function getContentTypeFields()
    {
        return $this->contentTypeFields;
    }

    /**
     * Get the resource for the cache
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Sets the entry's meta title, description, and keywords to the theme
     *
     * @return self
     */
    public function setThemeMetadata()
    {
        if ($this->resource instanceof \CmsCanvas\Models\Content\Entry) {
            $this->resource->setThemeMetadata();
        }

        return $this;
    }

}

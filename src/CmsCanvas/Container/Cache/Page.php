<?php namespace CmsCanvas\Container\Cache;

use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Type;

class Page {

    /**
     * An Entry or Content Type
     *
     * @var mixed
     */
    protected $object;

    /**
     * Collection of content type fields with entry data
     *
     * @var \CmsCanvas\Models\Content\Type\Field|Collection
     */
    protected $contentTypeFields;

    /**
     * Defines the order in which to sort.
     *
     * @param int $objectId
     * @param string $objectType
     * @return void
     */
    public function __construct($objectId, $objectType = 'entry')
    {
        if ($objectType == 'contentType')
        {
            $this->object = Type::find($objectId);
        }
        else
        {
            $this->object = Entry::find($objectId);
            $this->object->contentType;
        }

        $this->contentTypeFields = $this->object->getContentTypeFields();
    }

    /**
     * Renders the cached page
     *
     * @param array $parameters
     * @return \CmsCanvas\StringView\StringView
     */
    public function render($parameters = array())
    {
        return $this->object->renderFromCache($this, $parameters);
    }

    /**
     * Returns an array of transalated data for the current entry or content type
     *
     * @return array
     */
    public function getRenderedData()
    {
        return $this->object->getRenderedData($this);
    }

    /**
     * Returns content type fields with entry data
     *
     * @return \CmsCanvas\Models\Content\Type\Field|Collection
     */
    public function getContentTypeFields()
    {
        return $this->contentTypeFields;
    }

}

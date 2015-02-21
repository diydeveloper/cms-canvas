<?php namespace CmsCanvas\Container\Cache;

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
     * @return \CmsCanvas\Content\Entry\Render|\CmsCanvas\Content\Type\Render
     */
    public function render($parameters = array())
    {
        $content = $this->object
            ->setCache($this)
            ->render($parameters);

        $layoutName = $content->getThemeLayout();

        if ($layoutName != null)
        {
            Theme::setLayout($layoutName);
            $layout = Theme::getLayout();
            $layout->content = $content;

            return $layout;
        }

        return $content;
    }


    /**
     * Get an array of transalated data for the current object
     *
     * @return array
     */
    public function getRenderedData()
    {
        return $this->object
            ->setCache($this)
            ->getRenderedData();
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

}

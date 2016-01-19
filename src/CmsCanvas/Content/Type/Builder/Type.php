<?php 

namespace CmsCanvas\Content\Type\Builder;

use StringView, Route;
use CmsCanvas\Models\Content\Type as ContentTypeModel;
use CmsCanvas\Content\Type\Render;

class Type {

    /**
     * @var \CmsCanvas\Models\Content\Type
     */
    protected $contentType;

    /**
     * Parameters added to the route
     *
     * @var array
     */
    protected $parameters;

    /**
     * Rendered data
     *
     * @var array
     */
    protected $renderedData;

    /**
     * Rendered content
     *
     * @var \TwigBridge\StringView\StringView
     */
    protected $renderContents;

    /**
     * Constructor
     *
     * @param  \CmsCanvas\Models\Content\Type  $contentType
     * @param  array $parameters
     * @param  array $renderedData
     * @return void
     */
    public function __construct(ContentTypeModel $contentType, $parameters = [])
    {
        $this->contentType = $contentType;
        $this->setParameters($parameters);
        $this->renderedData = $this->contentType->getRenderedData();
        $this->renderContents = $this->renderContents();
    }

    /**
     * Returns the content type model instance
     *
     * @return \CmsCanvas\Models\Content\Type
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Returns a render instance
     *
     * @return \CmsCanvas\Content\Type\Render
     */
    public function render()
    {
        $render = new Render($this);
        $this->setParameter('self', $render);

        return $render;
    }

    /**
     * Generates a view with the content type's data
     *
     * @return string
     */
    public function renderContents()
    {
        if ($this->renderContents != null) {
            return $this->renderContents;
        }

        $data = array_merge($this->renderedData, $this->parameters);

        $template = ($this->contentType->layout === null) ? '' : $this->contentType->layout;
        $content = StringView::make($template)
            ->cacheKey($this->contentType->getRouteName())
            ->updatedAt($this->contentType->updated_at->timestamp)
            ->with($data)
            ->prerender();

        return $content;
    }

    /**
     * Returns parameters and rendered data
     *
     * @param  string  $key
     * @return mixed
     */
    public function getData($key)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        if (isset($this->renderedData[$key])) {
            return $this->renderedData[$key];
        }        

        return null;
    }

    /**
     * Adds a parameter to the paramaters array
     *
     * @param  string  $key
     * @param  string  $value
     * @return self
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * Set the paramaters array
     *
     * @param  array  $value
     * @return self
     */
    protected function setParameters(array $parameters)
    {
        $route = Route::current();
        if ($route != null) {
            $this->parameters = array_merge($route->parameters(), $parameters);
        } else {
            $this->parameters = $parameters;
        }

        return $this;
    }

}

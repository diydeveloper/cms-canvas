<?php 

namespace CmsCanvas\Content\Type;

class Render {

    /**
     * The content type to render from
     *
     * @var string
     */
    protected $contentType;

    /**
     * Parameters added to the route
     *
     * @var array
     */
    protected $parameters;

    /**
     * Data that has already been provided
     *
     * @var array
     */
    protected $data;

    /**
     * Defines the column name to sort.
     *
     * @var string
     */
    protected $renderedData;

    /**
     * Defines the order in which to sort.
     *
     * @param \CmsCanvas\Models\Content\Type $contentType
     * @param array $parameters
     * @return void
     */
    public function __construct(\CmsCanvas\Models\Content\Type $contentType, $parameters = [], $data = [])
    {
        $this->contentType = $contentType;
        $this->parameters = $parameters;
        $this->data = $data;
    }

    /**
     * Magic method to retrive rendered data
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        // This will only render the data if the user make a get request
        if ($this->renderedData === null) {
            $data = (empty($this->data)) ? $this->contentType->getRenderedData() : $this->data;
            $this->renderedData = array_merge($data, $this->parameters);
        }

        if (isset($this->renderedData[$key])) {
            return $this->renderedData[$key];
        } else {
            return null;
        }
    }

    /**
     * Magic method to catch undefined methods
     *
     * @param string $name
     * @param array $arguments
     * @return void
     */
    public function __call($name, $arguments)
    {
        //
    }

    /**
     * Magic method to render the contentType as a string
     *
     * @return string
     */
    public function __toString()
    {
        $parameters = array_merge($this->parameters, ['self' => $this]);

        return (string) $this->contentType->renderContents($parameters, $this->data);
    }

    /**
     * Returns the full route for the content type
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->contentType->getRoute();
    }

    /**
     * Used to determine if the current render is an entry
     *
     * @return boolean
     */
    public function isEntry()
    {
        return false;
    }

    /**
     * Returns the theme layout to use
     *
     * @return string
     */
    public function getThemeLayout()
    {
        return $this->contentType->theme_layout;
    }

}
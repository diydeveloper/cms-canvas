<?php namespace CmsCanvas\Content\Entry;

class Render {

    /**
     * Defines the column name to sort.
     *
     * @var string
     */
    protected $entry;

    /**
     * Defines the column name to sort.
     *
     * @var string
     */
    protected $renderedData;

    /**
     * Set true if the entry is solo or first in a collection
     *
     * @var bool
     */
    protected $firstFlag = true;

    /**
     * Set true if the entry is solo or last in a collection
     *
     * @var bool
     */
    protected $lastFlag = true;

    /**
     * Defines the order in which to sort.
     *
     * @param \CmsCanvas\Models\Content\Entry $entry
     * @param array $parameters
     * @return void
     */
    public function __construct(\CmsCanvas\Models\Content\Entry $entry, $parameters = array())
    {
        $this->entry = $entry;
        $this->parameters = $parameters;
    }

    /**
     * Magic method to retrive rendered data
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if ($this->renderedData === null)
        {
            $this->renderedData = array_merge($this->entry->getRenderedData(), $this->parameters);
        }

        if (isset($this->renderedData[$key]))
        {
            return $this->renderedData[$key];
        }
        else
        {
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
     * Magic method to render the entry as a string
     *
     * @return string
     */
    public function __toString()
    {
        $parameters = array_merge($this->parameters, array('__entry' => $this));

        return (string) $this->entry->renderContents($parameters);
    }

    /**
     * @return bool
     */
    public function isFirst()
    {
        return $this->firstFlag;
    }

    /**
     * @return bool
     */
    public function isLast()
    {
        return $this->lastFlag;
    }

    /**
     * Sets the firstFlag class variable
     *
     * @param bool $value
     * @return bool
     */
    public function setFirstFlag($value)
    {
        return $this->firstFlag = (bool) $value;
    }

    /**
     * Sets the lastFlag class variable
     *
     * @param bool $value
     * @return bool
     */
    public function setLastFlag($value)
    {
        return $this->lastFlag = (bool) $value;
    }

}
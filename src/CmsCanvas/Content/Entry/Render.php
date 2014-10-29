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
     * The position the entry is in a collection
     *
     * @var bool
     */
    protected $index = 0;

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
     * Reutrns the firstFlag class property
     *
     * @return bool
     */
    public function isFirst()
    {
        return $this->firstFlag;
    }

    /**
     * Reutrns the lastFlag class property
     *
     * @return bool
     */
    public function isLast()
    {
        return $this->lastFlag;
    }

    /**
     * Reutrns the index class property
     *
     * @return int
     */
    public function index()
    {
        return $this->index;
    }

    /**
     * Sets the firstFlag class variable
     *
     * @param bool $value
     * @return void
     */
    public function setFirstFlag($value)
    {
        $this->firstFlag = (bool) $value;
    }

    /**
     * Sets the lastFlag class variable
     *
     * @param bool $value
     * @return void
     */
    public function setLastFlag($value)
    {
        $this->lastFlag = (bool) $value;
    }

    /**
     * Sets the entry's position in the collection
     *
     * @param int $value
     * @return void
     */
    public function setIndex($value)
    {
        $this->index = $value;
    }

}
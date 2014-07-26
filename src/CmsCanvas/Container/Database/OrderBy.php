<?php namespace CmsCanvas\Container\Database;

class OrderBy {

    /**
     * Defines the column name to sort.
     *
     * @var string
     */
    protected $column;

    /**
     * Defines the order in which to sort the column.
     *
     * @var string
     */
    protected $sort;

    /**
     * Defines the order in which to sort.
     *
     * @param mixed $argumentsArray
     * @return void
     */
    public function __construct($argumentsArray, $className)
    {
        $argumentsArray = (array) $argumentsArray;

        if (isset($argumentsArray['column']) && in_array($argumentsArray['column'], $className::getSortable()))
        {
            $this->column = $argumentsArray['column'];
        }
        else
        {
            $this->column = $className::getDefaultSortColumn();
        }

        if (isset($argumentsArray['sort']) && in_array($argumentsArray['sort'], array('asc', 'desc')))
        {
            $this->sort = $argumentsArray['sort'];
        }
        else 
        {
            $this->sort = $className::getDefaultSortOrder();
        }
    }

    /**
     * Returns the column class variable.
     *
     * @return string
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * Returns the sort class variable.
     *
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Returns the provided column's CSS class selector.
     *
     * @param string
     * @return string
     */
    public function getElementClass($column)
    {
        if ($column == $this->column)
        {
            return ' ' . $this->sort;
        }

        return '';
    }

}

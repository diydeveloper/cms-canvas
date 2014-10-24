<?php namespace CmsCanvas\Database\Eloquent;

use Eloquent, Session, Input, stdClass, Config, Auth;
use CmsCanvas\Container\Database\OrderBy;
use CmsCanvas\Database\Eloquent\Relations\Pivot;

class Model extends Eloquent {

    /**
     * Returns an instance of BasePivot when using pivot tables
     *
     * @return \CmsCanvas\Models\BasePivot
     */
    public function newPivot(Eloquent $parent, array $attributes, $table, $exists)
    {
        return new Pivot($parent, $attributes, $table, $exists);
    }

    /**
     * Returns an instance of BaseCollection when using collections
     *
     * @return \CmsCanvas\BaseCollection
     */
    public function newCollection(array $models = array())
    {
        return new \CmsCanvas\Database\Eloquent\Collection($models);
    }

   /**
     * Forget the filter object saved in the session
     *
     * @return void|static
     */
    public static function processFilterRequest()
    {
        $class = get_called_class();

        if (Input::get('clear_filter'))
        {
            $class::forgetSessionFilter();
        }
        else if (Input::get('filter'))
        {
            $class::setSessionFilter(Input::get('filter'));
        }
        else if (is_array(Input::get('orderBy')))
        {
            $class::setSessionOrderBy(Input::get('orderBy'));
        }
    }

   /**
     * Forget the filter object saved in the session
     *
     * @return void|static
     */
    public static function forgetSessionFilter()
    {
        $class = get_called_class();

        Session::forget($class.'::filter');
    }

    /**
     * Saves the provided filter object in the session
     *
     * @param mixed $filter
     * @return void|static
     */
    public static function setSessionFilter($filter)
    {
        $class = get_called_class();

        $filter = (object) $filter;

        Session::put($class.'::filter', $filter);
    }

    /**
     * Returns the filter object saved in the session
     *
     * @return object|static
     */
    public static function getSessionFilter()
    {
        $class = get_called_class();

        $filter = Session::get($class.'::filter');

        if (empty($filter))
        {
            $filter = new stdClass();
        } 

        return $filter;
    }

    /**
     * Saves the provided order by object in the session
     *
     * @param mixed $orderBy
     * @return void|static
     */
    public static function setSessionOrderBy($orderBy)
    {
        $class = get_called_class();

        $orderBy = new OrderBy($orderBy, $class);

        Session::put($class.'::orderBy', $orderBy);
    }

    /**
     * Returns the order by object saved in the session
     *
     * @return \Cmscanvas\Container\Database\OrderBy|static
     */
    public static function getSessionOrderBy()
    {
        $class = get_called_class();
        
        $orderBy = Session::get($class.'::orderBy');

        if ( ! ($orderBy instanceof OrderBy))
        {
            $orderBy = new OrderBy(null, $class);
        }

        return $orderBy;
    } 

    /**
     * Returns an array of columns that can be sorted
     *
     * @return array|static
     */
    public static function getSortable()
    {
        $class = get_called_class();

        return $class::$sortable;
    }

    /**
     * Returns the column to sort on if no column is specified
     *
     * @return string|static
     */
    public static function getDefaultSortColumn()
    {
        $class = get_called_class();

        return $class::$defaultSortColumn;
    }

    /**
     * Returns the column to sort on if no column is specified
     *
     * @return string|static
     */
    public static function getDefaultSortOrder()
    {
        $class = get_called_class();

        if (isset($class::$defaultSortOrder))
        {
            return $class::$defaultSortOrder;
        }
        else
        {
            return 'asc';
        }
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array  $attributes
     * @param  array  $takeLiteralValues
     * @return \Illuminate\Database\Eloquent\Model|static
     *
     * @throws MassAssignmentException
     */
    public function fill(array $attributes, array $takeLiteralValues = array())
    {
        foreach ($attributes as $key => $value)
        {
            if ($value === '' && !in_array($key, $takeLiteralValues))
            {
                $attributes[$key] = null;
            }
        }

        return parent::fill($attributes);
    }

}

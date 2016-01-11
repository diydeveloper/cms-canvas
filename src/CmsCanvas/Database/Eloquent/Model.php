<?php 

namespace CmsCanvas\Database\Eloquent;

use Eloquent, Session, Input, stdClass, Config, Auth;
use CmsCanvas\Container\Database\OrderBy;
use CmsCanvas\Database\Eloquent\Relations\Pivot;
use CmsCanvas\Database\Eloquent\Collection;
use Carbon\Carbon;
use DateTime;

class Model extends Eloquent {

    /**
     * Returns an instance of BasePivot when using pivot tables
     *
     * @return \CmsCanvas\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(Eloquent $parent, array $attributes, $table, $exists)
    {
        return new Pivot($parent, $attributes, $table, $exists);
    }

    /**
     * Returns an instance of BaseCollection when using collections
     *
     * @return \CmsCanvas\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }

   /**
     * Forget the filter object saved in the session
     *
     * @return void|static
     */
    public static function processFilterRequest()
    {
        $class = get_called_class();

        if (Input::get('clear_filter')) {
            $class::forgetSessionFilter();
        } elseif (Input::get('filter')) {
            $class::setSessionFilter(Input::get('filter'));
        } elseif (is_array(Input::get('orderBy'))) {
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

        if (empty($filter)) {
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

        if ( ! ($orderBy instanceof OrderBy)) {
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

        if (isset($class::$defaultSortOrder)) {
            return $class::$defaultSortOrder;
        } else {
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
    public function fill(array $attributes, array $takeLiteralValues = [])
    {
        foreach ($attributes as $key => $value) {
            if ($value === '' && !in_array($key, $takeLiteralValues)) {
                $attributes[$key] = null;
            }
        }

        return parent::fill($attributes);
    }

    /**
     * Convert a DateTime to a storable string.
     *
     * @param  \DateTime|int  $value
     * @return string
     */
    public function fromDateTime($value, $timezone = null)
    {
        $format = $this->getDateFormat();

        $value = $this->asDateTime($value, $timezone);

        return $value->format($format);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value, $timezone = null)
    {
        // If this value is already a Carbon instance, we shall just return it as is.
        // This prevents us having to reinstantiate a Carbon instance when we know
        // it already is one, which wouldn't be fulfilled by the DateTime check.
        if ($value instanceof Carbon) {
            return $value;
        }

        // If the value is already a DateTime instance, we will just skip the rest of
        // these checks since they will be a waste of time, and hinder performance
        // when checking the field. We will just return the DateTime right away.
        if ($value instanceof DateTime) {
            return Carbon::instance($value);
        }

        // If this value is an integer, we will assume it is a UNIX timestamp's value
        // and format a Carbon object from this timestamp. This allows flexibility
        // when defining your date fields as they might be UNIX timestamps here.
        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value, $timezone);
        }

        // If the value is in simply year, month, day format, we will instantiate the
        // Carbon instances from that format. Again, this provides for simple date
        // fields on the database, while still supporting Carbonized conversion.
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value)) {
            return Carbon::createFromFormat('Y-m-d', $value, $timezone)->startOfDay();
        }

        // Finally, we will just assume this date is in the format used by default on
        // the database connection and use that format to create the Carbon object
        // that is returned back out to the developers after we convert it here.
        return Carbon::createFromFormat($this->getDateFormat(), $value, $timezone);
    }

}

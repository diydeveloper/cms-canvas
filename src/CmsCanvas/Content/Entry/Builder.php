<?php 

namespace CmsCanvas\Content\Entry;

use Auth, Lang, DB;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Entry\Status;
use CmsCanvas\Content\Entry\RenderCollection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use CmsCanvas\Content\Entry\Builder\WhereClause;
use CmsCanvas\Exceptions\Exception;

class Builder {

    /**
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $entries;

    /**
     * @var int
     */
    protected $paginate;

    /**
     * @var int
     */
    protected $simplePaginate;

    /**
     * ID of a specific entry
     *
     * @var \CmsCanvas\Content\Entry\Builder\WhereClause
     */
    protected $entryIds;

    /**
     * Short name of a content type
     *
     * @var \CmsCanvas\Content\Entry\Builder\WhereClause
     */
    protected $contentTypes;

    /**
     * Filter entries by year
     *
     * @var \CmsCanvas\Content\Entry\Builder\WhereClause
     */
    protected $year;

    /**
     * Filter entries by month
     *
     * @var \CmsCanvas\Content\Entry\Builder\WhereClause
     */
    protected $month;

    /**
     * Filter entries by day
     *
     * @var \CmsCanvas\Content\Entry\Builder\WhereClause
     */
    protected $day;

    /**
     * Filter entries by url title
     *
     * @var \CmsCanvas\Content\Entry\Builder\WhereClause
     */
    protected $urlTitle;

    /**
     * Short name of a content type
     *
     * @var array
     */
    protected $orders;

    /**
     * Short name of a content type
     *
     * @var array
     */
    protected $sorts;

    /**
     * Short name of a content type
     *
     * @var int
     */
    protected $offset;

    /**
     * Short name of a content type
     *
     * @var int
     */
    protected $limit;

    /**
     * Short name of a content type
     *
     * @var \CmsCanvas\Content\Entry\Builder\WhereClause
     */
    protected $wheres;

    /**
     * Abort status code if entries not found
     *
     * @var int
     */
    protected $noResultsAbort;

    /**
     * The date field used for date arguments
     *
     * @var int
     */
    protected $dateBy = 'entries.created_at_local';

    /**
     * @var array
     */
    protected $joinedEntryDataAliases = [];

    /**
     * @var array
     */
    protected $sortableColumns = [
        'id',
        'title', 
        'url_title', 
        'created_at', 
        'updated_at', 
        'created_at_local', 
        'updated_at_local', 
    ];

    /**
     * @param array $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->buildFromArray($config);
    }

    /**
     * Returns collection of entries
     *
     * @return \CmsCanvas\Content\Entry\RenderCollection
     */
    public function get()
    {
        $this->compile();

        if ($this->paginate !== null) {
            $entries = $this->entries->paginate($this->paginate);
        } elseif ($this->simplePaginate !== null) {
            $entries = $this->entries->simplePaginate($this->paginate);
        } else {
            $entries = $this->entries->get();
        }

        if ($this->noResultsAbort != null && count($entries) <= 0) {
            abort($this->noResultsAbort);
        }

        $paginator = null;
        if ($entries instanceof \Illuminate\Pagination\AbstractPaginator) {
            $paginator = $entries;
        }

        $entryBuilders = Entry::newEntryBuilderCollection($entries);

        return new RenderCollection($entryBuilders, $paginator);
    }

    /**
     * Construct the object from an array
     *
     * @param array $config
     * @return void
     */
    protected function buildFromArray(array $config)
    {
        foreach ($config as $key => $value) {
            switch ($key) {
                case 'entry_id':
                    $this->setEntryIds($value);
                    break;

                case 'content_type':
                    $this->setContentTypes($value);
                    break;

                case 'order_by':
                    $this->setOrders($value);
                    break;

                case 'sort':
                    $this->setSorts($value);
                    break;
                
                case 'limit':
                    $this->setLimit($value);
                    break;

                case 'offset':
                    $this->setOffset($value);
                    break;

                case 'paginate':
                    $this->paginate = $value;
                    break;

                case 'simple_paginate':
                    $this->simplePaginate = $value;
                    break;

                case 'where':
                    $this->setWheres($value);
                    break;

                case 'year':
                    $this->setYear($value);
                    break;

                case 'url_title':
                    $this->setUrlTitle($value);
                    break;

                case 'month':
                    $this->setMonth($value);
                    break;

                case 'day':
                    $this->setDay($value);
                    break;

                case 'no_results_abort':
                    $this->setNoResultsAbort($value);
                    break;
            }
        } 
    }

    /**
     * Set query where clause for specific content type
     *
     * @param  string $contentTypes
     * @return self
     */
    protected function setContentTypes($contentTypes)
    {
        if ($contentTypes === null || $contentTypes === '') {
            $this->contentTypes = null;
        } else {
            $this->contentTypes = $this->parseStringValues('short_name', $contentTypes);
        }

        return $this;
    }

    /**
     * Set query where clause for specific year
     *
     * @param  string $year
     * @return self
     */
    protected function setYear($year)
    {
        if ($year === null || $year === '') {
            $this->year = null;
        } else {
            $this->year = $this->parseStringValues(DB::raw("YEAR({$this->dateBy})"), $year);
        }

        return $this;
    }

    /**
     * Set query where clause for specific month
     *
     * @param  string $month
     * @return self
     */
    protected function setMonth($month)
    {
        if ($month === null || $month === '') {
            $this->month = null;
        } else {
            $this->month = $this->parseStringValues(DB::raw("MONTH({$this->dateBy})"), $month);
        }

        return $this;
    }

    /**
     * Set query where clause for specific day
     *
     * @param  string $day
     * @return self
     */
    protected function setDay($day)
    {
        if ($day === null || $day === '') {
            $this->day = null;
        } else {
            $this->day = $this->parseStringValues(DB::raw("DAY({$this->dateBy})"), $day);
        }

        return $this;
    }

    /**
     * Set query where clause for specific url title
     *
     * @param  string $urlTitle
     * @return self
     */
    protected function setUrlTitle($urlTitle)
    {
        if ($urlTitle === null || $urlTitle === '') {
            $this->urlTitle = null;
        } else {
            $this->urlTitle = $this->parseStringValues('entries.url_title', $urlTitle);
        }

        return $this;
    }

    /**
     * Set query limit for entries
     *
     * @param  int $limit
     * @return self
     */
    protected function setLimit($limit)
    {
        if ($limit === null || $limit === '') {
            $this->limit = null;
        } else {
            $this->limit = $limit;
        }

        return $this;
    }

    /**
     * Set query offset for entries
     *
     * @param  int $offset
     * @return self
     */
    protected function setOffset($offset)
    {
        if ($offset === null || $offset === '') {
            $this->offset = null;
        } else {
            $this->offset = $offset;
        }

        return $this;
    }

    /**
     * Set abort status code if no results found
     *
     * @param  int $statusCode
     * @return self
     */
    protected function setNoResultsAbort($statusCode)
    {
        if ($statusCode === null || $statusCode === '') {
            $this->noResultsAbort = null;
        } else {
            $this->noResultsAbort = $statusCode;
        }

        return $this;
    }

    /**
     * Set query where clause for specific entry id
     *
     * @param  string  $entryIds
     * @return self
     */
    protected function setEntryIds($entryIds)
    {
        if ($entryIds === null || $entryIds === '') {
            $this->entryIds = null;
        } else {
            $this->entryIds = $this->parseStringValues('entries.id', $entryIds);
        }

        return $this;
    }

    /**
     * Set query order by for entries
     *
     * @param  string $orderBy
     * @return self
     */
    protected function setOrders($orderBy)
    {
        if ($orderBy === null || $orderBy === '') {
            $this->orders = null;
        } else {
            $this->orders = $this->parseDelimitedString($orderBy);
        }

        return $this;
    }

    /**
     * Set query order by sort by for entries
     *
     * @param  string $sort
     * @return self
     */
    protected function setSorts($sort)
    {
        if ($sort === null || $sort === '') {
            $this->sorts = null;
        } else {
            $this->sorts = $this->parseDelimitedString($sort);
        }

        return $this;
    }

    /**
     * Set query where clause for entries
     *
     * @param  array $where
     * @return self
     */
    protected function setWheres($wheres)
    {
        if ($wheres === null || $wheres === '') {
            $this->wheres = null;

            return $this;
        }

        if (! is_array($wheres)) {
            throw new Exception('Where clause must be an array.');
        }

        if (! is_array(current($wheres))) {
            $wheres = [$wheres];
        }

        $whereClause = new WhereClause();
        $whereClause->createNestedEntryData($wheres);
        $this->wheres[] = $whereClause;

        return $this;
    }

    /**
     * Joins entry_data table in order to filter or sort entries
     *
     * @return self
     */
    protected function joinEntryData($alias = 'entry_data', WhereClause $whereClause = null)
    {
        if (isset($this->joinedEntryDataAliases[$alias])) {
            return;
        }

        $this->joinedEntryDataAliases[$alias] = $alias;

        $this->entries->leftJoin("entry_data as {$alias}", function($join) use ($alias, $whereClause) {
            $join->on('entries.id', '=' , $alias.'.entry_id')
                ->where($alias.'.language_locale', '=', Lang::getLocale());

            if ($whereClause != null) {
                $whereClause->build($join);
            }
        })
        ->groupBy('entries.id');        
    }

    /**
     * Parse string to build an operational where clause
     *
     * @param  string $column
     * @param  string $string
     * @return array
     */
    protected function parseStringValues($column, $string)
    {
        $not = false;
        if (stripos($string, 'not ') === 0) {
            $not = true;
            $string = substr($string, 4);
        }

        $values = $this->parseDelimitedString($string);

        if (count($values) > 1) {
            $operator = ($not == true) ? 'not in' : 'in';
        } else {
            $operator = ($not == true) ? '!=' : '=';
            $values = current($values);
        }

        return new WhereClause($column, $operator, $values);
    }

    /**
     * Convert a pipe delimited string to an array
     *
     * @return array
     */
    protected function parseDelimitedString($string, $delimiter = '|')
    {
        return explode($delimiter, $string);
    }

    /**
     * Adds content type filter to entries query
     *
     * @return void
     */
    protected function compileContentTypes()
    {
        if (count($this->contentTypes) > 0) {
            $builder = $this;

            $this->entries->whereHas('contentType', function($query) use ($builder) {
                $builder->contentTypes->build($query);
            });
        }
    }

    /**
     * Adds entry ids filter to the entries query
     *
     * @return void
     */
    protected function compileEntryIds()
    {
        if ($this->entryIds != null) {
            $this->entryIds->build($this->entries);
        }
    }

    /**
     * Adds year filter to the entries query
     *
     * @return void
     */
    protected function compileYear()
    {
        if ($this->year != null) {
            $this->year->build($this->entries);
        }
    }


    /**
     * Adds month filter to the entries query
     *
     * @return void
     */
    protected function compileMonth()
    {
        if ($this->month != null) {
            $this->month->build($this->entries);
        }
    }

    /**
     * Adds day filter to the entries query
     *
     * @return void
     */
    protected function compileDay()
    {
        if ($this->day != null) {
            $this->day->build($this->entries);
        }
    }

    /**
     * Adds url title filter to the entries query
     *
     * @return void
     */
    protected function compileUrlTitle()
    {
        if ($this->urlTitle != null) {
            $this->urlTitle->build($this->entries);
        }
    }

    /**
     * Adds order by to the entries query
     *
     * @return void
     */
    protected function compileOrders()
    {
        if (count($this->orders) > 0) {
            $counter = 0;
            foreach ($this->orders as $orderBy) {
                if (in_array($orderBy, $this->sortableColumns)) {
                    $field = $orderBy;
                } else {
                    $alias = $orderBy.'_orderBy';
                    $whereClause = new WhereClause($alias.'.content_type_field_short_tag', '=', $orderBy);
                    $this->joinEntryData($alias, $whereClause);
                    $field = $alias.'.data';
                }

                $sort = (isset($this->sorts[$counter]) && strtolower($this->sorts[$counter]) == 'desc') ? 'desc' : 'asc';
                $this->entries->orderBy($field, $sort);
                $counter++;
            }
        }
    }

    /**
     * Adds order by to the entries query
     *
     * @return void
     */
    protected function compileWheres()
    {
        if (count($this->wheres) > 0) {
            $this->joinEntryData();

            foreach ($this->wheres as $whereClause) {
                $whereClause->build($this->entries);
            }
        }
    }

    /**
     * Adds limit to the entries query
     *
     * @return void
     */
    protected function compileLimit()
    {
        if ($this->limit != null) {
            $this->entries->take($this->limit);
        }
    }

    /**
     * Adds offset to the entries query
     *
     * @return void
     */
    protected function compileOffset()
    {
        if ($this->offset != null) {
            $this->entries->skip($this->offset);
        }
    }

    /**
     * Adds status filter to entries query
     *
     * @return void
     */
    protected function compileStatusFilter()
    {
        // May need to rethink this as it causes several queries
        if (Auth::check() && Auth::user()->can('ADMIN_ENTRY_VIEW')) {
            $this->entries->whereIn('entry_status_id', [Status::PUBLISHED, Status::DRAFT]);
        } else {
            $this->entries->where('entry_status_id', Status::PUBLISHED);
        }
    }

    /**
     * Compile the entries query from the current object
     *
     * @return void
     */
    protected function compile()
    {
        $entries = new Entry;
        $this->entries = $entries->select('entries.*');

        $this->compileContentTypes();
        $this->compileEntryIds();
        $this->compileYear();
        $this->compileMonth();
        $this->compileDay();
        $this->compileUrlTitle();
        $this->compileOrders();
        $this->compileWheres();
        $this->compileStatusFilter();
        $this->compileLimit();
        $this->compileOffset();
    }

}

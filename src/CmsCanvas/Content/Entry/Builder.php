<?php 

namespace CmsCanvas\Content\Entry;

use Auth;
use CmsCanvas\Models\Content\Entry;
use CmsCanvas\Models\Content\Entry\Status;
use CmsCanvas\Content\Entry\RenderCollection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @var stdClass
     */
    protected $entryIds;

    /**
     * Short name of a content type
     *
     * @var stdClass
     */
    protected $contentTypes;

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
     * @var string
     */
    protected $where;

    /**
     * Pivots
     *
     * @var array
     */
    protected $pivots = [];

    /**
     * @var boolean
     */
    protected $pivotTablesIncluded = false;

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
            // $entries = $this->entries->paginate($this->paginate);
            $entries = $this->paginate($this->paginate);
        } elseif ($this->simplePaginate !== null) {
            $entries = $this->simplePaginate($this->simplePaginate);
        } else {
            $entries = $this->entries->get();
        }

        return new RenderCollection($entries);
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
                    $this->setWhere($value);
                    break;
            }
        } 
    }

    /**
     * Set query where clause for specific content type
     *
     * @param int $entryId
     * @return self
     */
    protected function setContentTypes($contentTypes)
    {
        $this->contentTypes = $this->parseStringValues($contentTypes);
    }

    /**
     * Set query limit for entries
     *
     * @param int $limit
     * @return self
     */
    protected function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Set query offset for entries
     *
     * @param int $offset
     * @return self
     */
    protected function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Set query where clause for specific entry id
     *
     * @param int $entryId
     * @return self
     */
    protected function setEntryIds($entryIds)
    {
        $this->entryIds = $this->parseStringValues($entryIds);

        return $this;
    }

    /**
     * Set query order by for entries
     *
     * @param string $orderBy
     * @return self
     */
    protected function setOrders($orderBy)
    {
        $this->orders = $this->parseDelimitedString($orderBy);

        return $this;
    }

    /**
     * Set query order by sort by for entries
     *
     * @param string $sort
     * @return self
     */
    protected function setSorts($sort)
    {
        $this->sorts = $this->parseDelimitedString($sort);

        return $this;
    }

    /**
     * Set query where clause for entries
     *
     * @param string $where
     * @return self
     */
    protected function setWhere($where)
    {
        $this->where = $where;

        return $this;
    }

    /**
     * Adds a pivot column to the entries query
     *
     * @param string $shortTag
     * @return self
     */
    protected function addPivot($shortTag)
    {
        if (! isset($this->pivots[$shortTag])) {
            $this->includePivotTables();

            $pivotExpression = \DB::raw('MAX(IF(`content_type_fields`.`short_tag` = \''.$shortTag.'\', `entry_data`.`data`, NULL)) AS '.$shortTag);

            $this->entries->addSelect($pivotExpression);

            $this->pivots[$shortTag] = $pivotExpression;
        }

        return $this;
    }

    /**
     * Joins tables required to pivot data to the entries query
     *
     * @return self
     */
    protected function includePivotTables()
    {
        if ($this->pivotTablesIncluded == false) {
            $locale = \Lang::getLocale();
            
            $this->entries->leftJoin(
                \DB::raw(
                    '(`entry_data` inner join `languages` on `entry_data`.`language_id` = `languages`.`id`'
                    . ' and `languages`.`locale` = \''.$locale.'\' inner join `content_type_fields` ON'
                    . ' `entry_data`.`content_type_field_id` = `content_type_fields`.`id`)'
                ), 
                'entries.id',
                '=',
                'entry_data.entry_id'
            )
            ->groupBy('entries.id');

            $this->pivotTablesIncluded = true;
        }

        return $this;
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int  $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function paginate($perPage = 15)
    {
        $page = Paginator::resolveCurrentPage();

        $total = $this->getCountForPagination();

        $results = $this->entries->forPage($page, $perPage)->get();

        return new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath()
        ]);
    }

    /**
     * Get a paginator only supporting simple next and previous links.
     *
     * This is more efficient on larger data-sets, etc.
     *
     * @param  int  $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    protected function simplePaginate($perPage = 15)
    {
        $page = Paginator::resolveCurrentPage();

        $this->skip(($page - 1) * $perPage)->take($perPage + 1);

        return new Paginator($this->get(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath()
        ]);
    }

    /**
     * Get the count of the total records for the paginator.
     *
     * @return int
     */
    public function getCountForPagination()
    {
        $this->backupFieldsForCount();

        $columns = array_merge([\DB::raw('count(DISTINCT `entries`.`id`) as __aggregate_count')], $this->pivots);

        $results = $this->entries->get($columns);

        $this->restoreFieldsForCount();

        if (isset($results[0])) {
            return $results[0]->__aggregate_count;
        }
    }

    /**
     * Backup some fields for the pagination count.
     *
     * @return void
     */
    protected function backupFieldsForCount()
    {
        $query = $this->entries->getQuery();

        foreach (['columns', 'groups', 'orders', 'limit', 'offset'] as $field) {
            $this->backups[$field] = $query->{$field};

            $query->{$field} = null;
        }
    }

    /**
     * Restore some fields after the pagination count.
     *
     * @return void
     */
    protected function restoreFieldsForCount()
    {
        $query = $this->entries->getQuery();

        foreach (['columns', 'groups', 'orders', 'limit', 'offset'] as $field) {
            $query->{$field} = $this->backups[$field];
        }

        $this->backups = [];
    }

    /**
     * Parse string to build an operational array of values
     *
     * @return array
     */
    protected function parseStringValues($string)
    {
        $delimiter = '|';
        $not = false;

        if (stripos($string, 'not ') === 0) {
            $not = true;
            $string = substr($string, 4);
        }

        $whereContainer = new \stdClass;
        $whereContainer->values = $this->parseDelimitedString($string, $delimiter);
        $whereContainer->not = $not;

        return $whereContainer;
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
            $entries = $this;

            $this->entries->whereHas('contentType', function($query) use($entries) {
                $entries->buildWhere('short_name', $entries->contentTypes, $query);
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
            $this->buildWhere('id', $this->entryIds);
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
                $this->addPivot($orderBy);
                $sort = (isset($this->sorts[$counter]) && $this->sorts[$counter] == 'desc') ? 'desc' : 'asc';
                $this->entries->orderBy($orderBy, $sort);
                $counter++;
            }
        }
    }

    /**
     * Adds order by to the entries query
     *
     * @return void
     */
    protected function compileWhere()
    {
        if ($this->where != null) {
            // $this->entries->having(\DB::raw($this->where));
            $this->entries->having('sort_order', '>', 3);
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
     * Selects the appropriate where clause to use based on the provided container
     * and adds it to the entries query
     *
     * @return void
     */
    protected function buildWhere($column, $whereContainer, $query = null)
    {
        if ($query == null) {
            $query = $this->entries;
        }

        if (count($whereContainer->values) > 0) {
            $query->whereIn($column, $whereContainer->values, 'and', $whereContainer->not);
        } else {
            $query->where($column, current($whereContainer->values));
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
        $this->compileOrders();
        $this->compileWhere();
        $this->compileStatusFilter();
        $this->compileLimit();
        $this->compileOffset();
    }

}
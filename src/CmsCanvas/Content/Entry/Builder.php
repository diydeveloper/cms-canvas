<?php 

namespace CmsCanvas\Content\Entry;

use Auth, Lang;
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
     * @var array
     */
    protected $joinedEntryDataAliases = [];

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
        $this->contentTypes = $this->parseStringValues('short_name', $contentTypes);
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
     * @param  string  $entryIds
     * @return self
     */
    protected function setEntryIds($entryIds)
    {
        $this->entryIds = $this->parseStringValues('id', $entryIds);

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
    protected function setWheres($wheres)
    {
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
     * Adds order by to the entries query
     *
     * @return void
     */
    protected function compileOrders()
    {
        if (count($this->orders) > 0) {
            $counter = 0;
            foreach ($this->orders as $orderBy) {
                $alias = $orderBy.'_orderBy';

                $whereClause = new WhereClause($alias.'.content_type_field_short_tag', '=', $orderBy);
                $this->joinEntryData($alias, $whereClause);

                $sort = (isset($this->sorts[$counter]) && strtolower($this->sorts[$counter]) == 'desc') ? 'desc' : 'asc';
                $this->entries->orderBy($alias.'.data', $sort);
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
        $this->compileOrders();
        $this->compileWheres();
        $this->compileStatusFilter();
        $this->compileLimit();
        $this->compileOffset();
    }

}
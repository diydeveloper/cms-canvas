<?php 

namespace CmsCanvas\Models\Content;

use CmsCanvas\Database\Eloquent\Model;

class Navigation extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'navigations';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = ['title', 'short_name'];

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * The columns that can sorted with the query builder orderBy method.
     *
     * @var array
     */
    protected static $sortable = ['title', 'short_name'];

    /**
     * The column to sort by if no session order by is defined.
     *
     * @var string
     */
    protected static $defaultSortColumn = 'title';

    /**
     * Sets data order by using a custom object
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  \CmsCanvas\Container\Database\OrderBy $orderBy
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeApplyOrderBy($query, \CmsCanvas\Container\Database\OrderBy $orderBy)
    {
        if (in_array($orderBy->getColumn(), self::$sortable)) {
            $query->orderBy($orderBy->getColumn(), $orderBy->getSort()); 
        }

        return $query;
    } 

    /**
     * Filters and queries using a custom object
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  object  $filter
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeApplyFilter($query, $filter)
    {
        if (isset($filter->search) && $filter->search != '') {
            $query->where('title', 'LIKE', "%{$filter->search}%");
        }

        return $query;
    }

}
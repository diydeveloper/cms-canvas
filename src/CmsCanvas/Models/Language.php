<?php 

namespace CmsCanvas\Models;

use CmsCanvas\Database\Eloquent\Model;
use CmsCanvas\Exceptions\Exception;

class Language extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'languages';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = [
        'language', 
        'locale',
        'active',
    ];

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
    protected static $sortable = ['language', 'locale'];

    /**
     * The column to sort by if no session order by is defined.
     *
     * @var string
     */
    protected static $defaultSortColumn = 'language';

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::saving(function($language) {
            $language->validateForSave();
        });

        self::deleting(function($language) {
            $language->validateForDeletion();
        });
    }

    /**
     * Validate the current object before saving
     *
     * @throws \CmsCanvas\Exceptions\Exception
     * @return void
     */
    public function validateForSave()
    {
        if ($this->default && ! $this->active) {
            throw new Exception("The default language can not be set to \"Inactive\"");
        }
    }

    /**
     * Validate the current object before deleting
     *
     * @throws \CmsCanvas\Exceptions\Exception
     * @return void
     */
    public function validateForDeletion()
    {
        if ($this->default) {
            throw new Exception("{$this->language} can not be deleted because it is set as the default language");
        }
    }

    /**
     * Sets data order by using a custom object
     *
     * @param Builder $query
     * @param OrderBy $orderBy
     * @return \Illuminate\Database\Eloquent\Builder|static
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
     * @param Builder $query
     * @param object $filter
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeApplyFilter($query, $filter)
    {
        if (isset($filter->search) && $filter->search != '') {
            $query->where('language', 'LIKE', "%{$filter->search}%");
            $query->orWhere('locale', 'LIKE', "%{$filter->search}%");
        }

        if (isset($filter->active)) {
            $query->where('active', $filter->active);
        }

        return $query;
    }

}
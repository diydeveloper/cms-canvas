<?php namespace CmsCanvas\Models\Content;

use Lang, StringView, stdClass, View, Cache;
use CmsCanvas\Database\Eloquent\Model;
use CmsCanvas\Content\Type\FieldType;
use CmsCanvas\Models\Language;

class Entry extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'entries';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = array(
        'title', 
        'route',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'entry_status_id',
        'author_id',
    );

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = array('id', 'created_at', 'updated_at');

    /**
     * The columns that can sorted with the query builder orderBy method.
     *
     * @var array
     */
    protected static $sortable = array(
        'id', 
        'title', 
        'route', 
        'content_type_title', 
        'entry_status_name', 
        'updated_at',
    );

    /**
     * The column to sort by if no session order by is defined.
     *
     * @var string
     */
    protected static $defaultSortColumn = 'updated_at';

    /**
     * The the sort order that the default column should be sorted by.
     *
     * @var string
     */
    protected static $defaultSortOrder = 'desc';

    /**
     * Defines a one to many relationship with content types
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentType()
    {
        return $this->belongsTo('\CmsCanvas\Models\Content\Type', 'content_type_id');
    }

    /**
     * Defines a has many through relationship with content type fields
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function contentTypeFields()
    {
        return $this->hasManyThrough('CmsCanvas\Models\Content\Type\Field', 'CmsCanvas\Models\Content\Type', 'id', 'content_type_id')
            ->with('type');
    }

    /**
     * Returns all data for all lanaguages for the current entry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allData()
    {
        return $this->hasMany('CmsCanvas\Models\Content\Entry\Data', 'entry_id', 'id')
            ->join('languages', 'entry_data.language_id', '=', 'languages.id')
            ->select('entry_data.*', 'languages.locale');
    }

    /**
     * Returns an array of transalated data for the current entry
     *
     * @return array
     */
    public function getRenderedData()
    {
        $query = $this->contentTypeFields()
            ->select('content_type_fields.*', 'entry_data.data')
            ->leftJoin('entry_data', 'entry_data.content_type_field_id', '=', 'content_type_fields.id')
            ->leftJoin('languages', 'entry_data.language_id', '=', 'languages.id')
            ->whereNull('languages.default')
            ->orWhere('languages.default', 1);

        $locale = Lang::getLocale();
        $fallbackLocale = Lang::getFallback();

        if ($locale != $fallbackLocale)
        {          
            $query->where('content_type_fields.translate', 0)
                ->orWhere(function($query) use ($locale)
                {
                    $query->where('content_type_fields.translate', 1)
                          ->where('languages.locale', $locale);
                });
        }

        $contentTypeFields = $query->get();

        $data = array();

        foreach ($contentTypeFields as $contentTypeField) 
        {
            $fieldType = FieldType::factory($contentTypeField, $this, $locale, $contentTypeField->data);
            $data[$contentTypeField->short_tag] = $fieldType->render();
        }

        $data['title'] = $this->title;

        return $data;
    }

    /**
     * Generates a view with the entry's data
     *
     * @return \CmsCanvas\StringView\StringView
     */
    public function render()
    {
        $data = $this->getRenderedData();

        return $this->contentType->render($data);
    }

    /**
     * Caches the entry's render
     *
     * @return \CmsCanvas\StringView\StringView
     */
    public function cacheRender()
    {
        $entry = $this;

        return Cache::rememberForever($this->getRouteName(), function() use($entry)
        {
            return $entry->render();
        });
    }

    /**
     * Returns the route name for the entry
     *
     * @return string
     */
    public function getRouteName()
    {
        $locale = Lang::getLocale();

        return 'entry.'.$this->id.'.'.$locale;
    }

    /**
     * Returns the full route for the entry
     *
     * @return string
     */
    public function getRoute()
    {
        if ($this->route)
        {
            return $this->contentType->getRoutePrefix().'/'.$this->route;
        }

        return '';
    }

    /**
     * Remove the first and last forward slash from the route
     *
     * @param string $value
     * @return string
     */
    public function getRouteAttribute($value)
    {
        return trim($value, '/');
    }

    /**
     * Remove the first and last forward slash from the route
     *
     * @param string $value
     * @return void
     */
    public function setRouteAttribute($value)
    {
        $value = trim($value, '/');

        if ($value === '')
        {
            $value = null;
        }

        $this->attributes['route'] = $value;
    }

    /**
     * Sets data order by using a custom object
     *
     * @param Builder $query
     * @param OrderBy $orderBy
     * @return Builder
     */
    public function scopeApplyOrderBy($query, \CmsCanvas\Container\Database\OrderBy $orderBy)
    {
        if (in_array($orderBy->getColumn(), self::$sortable))
        {
            $query->orderBy($orderBy->getColumn(), $orderBy->getSort()); 
        }

        return $query;
    } 

    /**
     * Filters and queries using a custom object
     *
     * @param Builder $query
     * @param object $filter
     * @return Builder
     */
    public function scopeApplyFilter($query, $filter)
    {
        if (isset($filter->search) && $filter->search != '')
        {
            $query->where('entries.title', 'LIKE', "%{$filter->search}%");
        }

        if ( ! empty($filter->content_type_id)) {
            $query->where('content_type_id', $filter->content_type_id); 
        }

        if ( ! empty($filter->entry_status_id)) {
            $query->where('entry_status_id', $filter->entry_status_id); 
        }

        return $query;
    }

}
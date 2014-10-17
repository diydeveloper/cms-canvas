<?php namespace CmsCanvas\Models\Content;

use Lang, StringView, View;
use Illuminate\Database\Query\Expression;
use CmsCanvas\Database\Eloquent\Model;
use CmsCanvas\Models\Language;
use CmsCanvas\Content\Type\FieldType;
use CmsCanvas\Database\Eloquent\Collection;
use CmsCanvas\Content\Type\FieldTypeCollection;

class Type extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'content_types';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = array(
        'title', 
        'layout', 
        'page_head', 
        'short_name', 
        'entries_allowed', 
        'route',
        'route_prefix',
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
    protected static $sortable = array('title', 'short_name');

    /**
     * The column to sort by if no session order by is defined.
     *
     * @var string
     */
    protected static $defaultSortColumn = 'title';

    /**
     * Defines a one to many relationship with entries
     *
     * @return HasMany
     */
    public function entries()
    {
        return $this->hasMany('\CmsCanvas\Models\Content\Entry', 'content_type_id');
    }

   /**
     * Defines a one to many relationship with entries
     *
     * @return HasMany
     */
    public function fields()
    {
        return $this->hasMany('\CmsCanvas\Models\Content\Type\Field', 'content_type_id');
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
        if ( isset($filter->search) && $filter->search != '')
        {
            $query->where('title', 'LIKE', "%{$filter->search}%")
                ->orWhere('short_name', 'LIKE', "%{$filter->search}%");
        }

        return $query;
    }

    /**
     * Queries and returns content types that are
     * are available to create new entires
     *
     * @return \CmsCanvas\Models\Content\Type|collection
     */
    public static function getAvailableForNewEntry()
    {
        return self::has('entries', '<', new Expression('content_types.entries_allowed'))
            ->orWhereNull('content_types.entries_allowed')
            ->orderBy('title', 'asc')
            ->get();
    }

    /**
     * Returns the route name for the content type
     *
     * @return string
     */
    public function getRouteName()
    {
        return 'contentType.'.$this->id;
    }

    /**
     * Returns the full route for the content type
     *
     * @return string
     */
    public function getRoute()
    {
        if ($this->route)
        {
            return $this->getRoutePrefix().'/'.$this->route;
        }

        return '';
    }

    /**
     * Returns the route prefix for the content type
     *
     * @return string
     */
    public function getRoutePrefix()
    {
        if ($this->route_prefix)
        {
            return '/'.$this->route_prefix;
        }

        return '';
    }

    /**
     * Remove the first and last forward slash from the route prefix
     *
     * @param string $value
     * @return string
     */
    public function getRoutePrefixAttribute($value)
    {
        return trim($value, '/');
    }

    /**
     * Remove the first and last forward slash from the route prefix
     *
     * @param string $value
     * @return void
     */
    public function setRoutePrefixAttribute($value)
    {
        $value = trim($value, '/');

        if ($value === '')
        {
            $value = null;
        }

        $this->attributes['route_prefix'] = $value;
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
     * Returns content type fields
     *
     * @return \CmsCanvas\Models\Content\Type\Field|Collection
     */
    public function getContentTypeFields()
    {
        return $this->fields()->with('type')->get();
    }

    /**
     * Returns an array of transalated data for the current content type
     *
     * @param \CmsCanvas\Container\Cache\Page $cache
     * @return array
     */
    public function getRenderedData(\CmsCanvas\Container\Cache\Page $cache = null)
    {
        if ($cache != null)
        {
            $contentTypeFields = $cache->getContentTypeFields();
        }
        else
        {
            $contentTypeFields = $this->getContentTypeFields();
        }

        $locale = Lang::getLocale();
        $data = array();

        foreach ($contentTypeFields as $contentTypeField) 
        {
            $fieldType = FieldType::factory($contentTypeField, null, $locale);
            $data[$contentTypeField->short_tag] = $fieldType->render();
        }

        $data['title'] = $this->title;

        return $data;
    }

    /**
     * Generates a view of the content type's layout
     *
     * @return \CmsCanvas\StringView\StringView
     */
    public function render($data = array())
    {
        if (empty($data))
        {
            $data = $this->getRenderedData();
        }

        $content = StringView::make(
            array(
                'template' => ($this->layout === null) ? '' : $this->layout, 
                'cache_key' => 'content.type.'.$this->id, 
                'updated_at' => time() - 1
            ), 
            $data
        ); 

        return $content;
    }

    /**
     * Renders a content type page from cache
     *
     * @param \CmsCanvas\Container\Cache\Page $cache
     * @param array $parameters
     * @return \CmsCanvas\StringView\StringView
     */
    public function renderFromCache(\CmsCanvas\Container\Cache\Page $cache, $parameters = array())
    {
        $data = $this->getRenderedData($cache);
        $data = array_merge($data, $parameters);

        return $this->render($data);
    }

    /**
     * Builds an array of views for administrative editing
     *
     * @param \CmsCanvas\Models\Content\Entry $entry
     * @return \Illuminate\View\View|array
     */
    public function getAdminFieldViews(\CmsCanvas\Models\Content\Entry $entry = null)
    {
        $contentTypeFields = $this->fields()
            ->with('type')
            ->orderByRaw('ISNULL(`sort`) asc')
            ->orderBy('sort', 'asc')
            ->get();

        $dataItems = ( ! empty($entry)) ? $entry->allData : new Collection();
        $languages = Language::all();

        $fieldViews = array();
        $locale = Lang::getLocale();

        foreach ($contentTypeFields as $contentTypeField)
        {
            $fieldDataItems = $dataItems->getWhere('content_type_field_id', $contentTypeField->id);

            $fieldType = FieldType::factory($contentTypeField, $entry, $locale);

            if ( ! $contentTypeField->translate)
            {
                $dataItem = $fieldDataItems->getFirstWhere('locale', $locale);
                $data = ($dataItem != null) ? $dataItem->data : '';
                $metadata = ($dataItem != null) ? $dataItem->metadata : '';

                $fieldType->setData($data);
                $fieldType->setMetadata($metadata);
            }

            $fieldViews[] = View::make('cmscanvas::admin.content.entry.editField')
                ->with('fieldType', $fieldType)
                ->with('languages', $languages)
                ->with('fieldDataItems', $fieldDataItems);
        }

        return $fieldViews;
    }

    /**
     * Builds a collection of all content field types for the 
     * current entry including translations
     *
     * @param \CmsCanvas\Models\Content\Entry $entry
     * @return \CmsCanvas\Content\Type\FieldType|FieldTypeCollection
     */
    public function getAllFieldTypeInstances(\CmsCanvas\Models\Content\Entry $entry = null)
    {
        $contentTypeFields = $this->fields()
            ->with('type')
            ->orderByRaw('ISNULL(`sort`) asc')
            ->orderBy('sort', 'asc')
            ->get();

        $languages = Language::all();
        $locale = Lang::getLocale();

        $fieldInstances = new FieldTypeCollection();

        foreach ($contentTypeFields as $contentTypeField)
        {
            if ($contentTypeField->translate)
            {
                foreach ($languages as $language)                
                {
                    $fieldInstances[] = FieldType::factory($contentTypeField, $entry, $language->locale);
                }
            }
            else
            {
                $fieldInstances[] = FieldType::factory($contentTypeField, $entry, $locale);
            }
        }

        return $fieldInstances;
    }

}
<?php namespace CmsCanvas\Models\Content;

use Lang, StringView, View, Auth;
use CmsCanvas\Content\Page\PageInterface;
use Illuminate\Database\Query\Expression;
use CmsCanvas\Database\Eloquent\Model;
use CmsCanvas\Models\Language;
use CmsCanvas\Content\Type\FieldType;
use CmsCanvas\Database\Eloquent\Collection;
use CmsCanvas\Content\Type\FieldTypeCollection;
use CmsCanvas\Content\Type\Render;

class Type extends Model implements PageInterface {

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
        'theme_layout',
        'admin_entry_view_permission_id',
        'admin_entry_edit_permission_id',
        'admin_entry_create_permission_id',
        'admin_entry_delete_permission_id',
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
     * An object used to retrive cached data
     *
     * @var \CmsCanvas\Container\Cache\Page
     */
    protected $cache;

    /**
     * Defines a one to many relationship with entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany('\CmsCanvas\Models\Content\Entry', 'content_type_id');
    }

   /**
     * Defines a one to many relationship with entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany('\CmsCanvas\Models\Content\Type\Field', 'content_type_id');
    } 

   /**
     * Defines relation to the admin view permission
     *
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function adminEntryViewPermission()
    {
        return $this->belongsTo('\CmsCanvas\Models\Permission', 'admin_entry_view_permission_id', 'id');
    }

   /**
     * Defines relation to the admin edit permission
     *
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function adminEntryEditPermission()
    {
        return $this->belongsTo('\CmsCanvas\Models\Permission', 'admin_entry_edit_permission_id', 'id');
    }

   /**
     * Defines relation to the admin create permission
     *
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function adminEntryCreatePermission()
    {
        return $this->belongsTo('\CmsCanvas\Models\Permission', 'admin_entry_create_permission_id', 'id');
    }

   /**
     * Defines relation to the admin delete permission
     *
     * @return \Illuminate\Database\Eloquent\BelongsTo
     */
    public function adminEntryDeletePermission()
    {
        return $this->belongsTo('\CmsCanvas\Models\Permission', 'admin_entry_delete_permission_id', 'id');
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
        return self::where(function($query) {
                $query->has('entries', '<', new Expression('content_types.entries_allowed'));
                $query->orWhereNull('content_types.entries_allowed');
            })
            ->where(function($query) {
                $query->whereNull('admin_entry_view_permission_id');

                $roles = Auth::user()->roles;
                if (count($roles) > 0)
                {
                    $query->orWhereHas('adminEntryViewPermission', function($query) use($roles)
                    {
                        $query->whereHas('roles', function($query) use($roles)
                        {
                            $query->whereIn('roles.id', $roles->lists('id'));
                        });
                    });
                }
            })
            ->orderBy('title', 'asc')
            ->get();
    }

    /**
     * Queries and returns all content types that the 
     * current user has permissions to view
     *
     * @return \CmsCanvas\Models\Content\Type|collection
     */
    public static function getAllViewable()
    {
        return self::where(function($query) {
                $query->whereNull('admin_entry_view_permission_id');

                $roles = Auth::user()->roles;
                if (count($roles) > 0)
                {
                    $query->orWhereHas('adminEntryViewPermission', function($query) use($roles)
                    {
                        $query->whereHas('roles', function($query) use($roles)
                        {
                            $query->whereIn('roles.id', $roles->lists('id'));
                        });
                    });
                }
            })
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
        if ($this->cache != null)
        {
            return $this->cache->getContentTypeFields();
        }

        return $this->fields()->with('type')->get();
    }

    /**
     * Returns an array of transalated data for the current content type
     *
     * @return array
     */
    public function getRenderedData()
    {
        $contentTypeFields = $this->getContentTypeFields();

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
     * @param array $parameters
     * @param array $data
     * @return \CmsCanvas\StringView\StringView
     */
    public function renderContents($parameters = array(), $data = array())
    {
        if (empty($data))
        {
            $data = $this->getRenderedData();
        }

        $data = array_merge($data, $parameters);

        StringView::extend(function($view, $compiler)
        {
            $pattern = $compiler->createMatcher('entries');

            return preg_replace($pattern, '<?php echo Content::entries($2) ?>', $view);
        });

        $content = StringView::make(
            array(
                'template' => ($this->layout === null) ? '' : $this->layout, 
                'cache_key' => $this->getRouteName(), 
                'updated_at' => $this->updated_at->timestamp
            ), 
            $data
        ); 

        return $content;
    }

    /**
     * Generates a view of the content type's layout
     *
     * @param array $parameters
     * @param array $data
     * @return \CmsCanvas\StringView\StringView
     */
    public function render($parameters = array(), $data = array())
    {
        return new Render($this, $parameters, $data);
    }

    /**
     * Sets a cache object used to render the content type
     *
     * @param \CmsCanvas\Container\Cache\Page $cache
     * @return self
     */
    public function setCache(\CmsCanvas\Container\Cache\Page $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Unsets the cache object on the current content type
     *
     * @return self
     */
    public function clearCache()
    {
        $this->cache = null;

        return $this;
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

    /**
     * Checks the number of entries the current content type has
     * compared to the allowed_entries setting.
     *
     * @throws RuntimeException
     * @return bool
     */
    public function checkEntriesAllowed()
    {
        if ($this->entries_allowed !== null 
            && $this->entries->count() >= $this->entries_allowed
        )
        {
            throw new \RuntimeException("
                The content type \"{$this->title}\" has the maximum number of allowed entries."
                . " (Max: $this->entries_allowed)"
            );
        }

        return true;
    }

    /**
     * Checks if current user has permissions to view the 
     * content type's entries.
     *
     * @return void
     */
    public function checkAdminEntryViewPermissions()
    {
        if ($this->adminEntryViewPermission != null)
        {
            Auth::user()->checkPermission($this->adminEntryViewPermission->key_name);
        }
    }

    /**
     * Checks if current user has permissions to edit the 
     * content type's entries.
     *
     * @return void
     */
    public function checkAdminEntryEditPermissions()
    {
        $this->checkAdminEntryViewPermissions();

        if ($this->adminEntryEditPermission != null)
        {
            Auth::user()->checkPermission($this->adminEntryEditPermission->key_name);
        }
    }

    /**
     * Checks if current user has permissions to create the 
     * content type's entries.
     *
     * @return void
     */
    public function checkAdminEntryCreatePermissions()
    {
        $this->checkAdminEntryViewPermissions();

        if ($this->adminEntryCreatePermission != null)
        {
            Auth::user()->checkPermission($this->adminEntryCreatePermission->key_name);
        }
    }

    /**
     * Checks if current user has permissions to delete the 
     * content type's entries.
     *
     * @return void
     */
    public function checkAdminEntryDeletePermissions()
    {
        $this->checkAdminEntryViewPermissions();

        if ($this->adminEntryDeletePermission != null)
        {
            Auth::user()->checkPermission($this->adminEntryDeletePermission->key_name);
        }
    }

}
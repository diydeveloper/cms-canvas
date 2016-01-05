<?php 

namespace CmsCanvas\Models\Content;

use Lang, Auth, StringView;
use CmsCanvas\Content\Page\PageInterface;
use Illuminate\Database\Query\Expression;
use CmsCanvas\Database\Eloquent\Model;
use CmsCanvas\Models\Language;
use CmsCanvas\Content\Type\FieldType;
use CmsCanvas\Database\Eloquent\Collection;
use CmsCanvas\Content\Type\FieldTypeCollection;
use CmsCanvas\Content\Type\Render;
use CmsCanvas\Content\Type\Builder\Type as ContentTypeBuilder;

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
    protected $fillable = [
        'title', 
        'layout', 
        'page_head', 
        'short_name', 
        'entries_allowed', 
        'max_revisions', 
        'route',
        'entry_uri_template',
        'theme_layout',
        'url_title_flag',
        'admin_entry_view_permission_id',
        'admin_entry_edit_permission_id',
        'admin_entry_create_permission_id',
        'admin_entry_delete_permission_id',
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
    protected static $sortable = ['title', 'short_name'];

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
     * Returns all revisions for the current content type
     * in order of newest to oldest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function revisions()
    {
        return $this->hasMany('CmsCanvas\Models\Content\Revision', 'resource_id', 'id')
            ->where('resource_type_id', Revision::CONTENT_TYPE_RESOURCE_TYPE_ID)
            ->orderBy('id', 'desc');
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
     * @return Builder
     */
    public function scopeApplyFilter($query, $filter)
    {
        if ( isset($filter->search) && $filter->search != '') {
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
                if (count($roles) > 0) {
                    $query->orWhereHas('adminEntryViewPermission', function($query) use($roles) {
                        $query->whereHas('roles', function($query) use($roles) {
                            $query->whereIn('roles.id', $roles->lists('id')->all());
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
                if (count($roles) > 0) {
                    $query->orWhereHas('adminEntryViewPermission', function($query) use($roles) {
                        $query->whereHas('roles', function($query) use($roles) {
                            $query->whereIn('roles.id', $roles->lists('id')->all());
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
        $locale = Lang::getLocale();

        return 'contentType.'.$this->id.'.'.$locale;
    }

    /**
     * Returns the full route for the content type
     *
     * @return string|null
     */
    public function getRoute()
    {
        if ($this->route !== null && $this->route !== '') {
            return '/'.$this->route;
        }

        return null;
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

        if ($value === '') {
            $value = null;
        }

        $this->attributes['route'] = $value;
    }

    /**
     * Remove the first and last forward slash from the entry_uri_template
     *
     * @param string $value
     * @return void
     */
    public function setEntryUriTemplateAttribute($value)
    {
        $value = trim($value, '/');

        if ($value === '') {
            $value = null;
        }

        $this->attributes['entry_uri_template'] = $value;
    }

    /**
     * Returns content type fields
     *
     * @param  bool $skipCacheFlag     
     * @return \CmsCanvas\Models\Content\Type\Field|Collection
     */
    public function getContentTypeFields($skipCacheFlag = false)
    {
        if (!$skipCacheFlag && $this->getCache() != null) {
            return $this->getCache()->getContentTypeFields();
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
        $data = [];

        foreach ($contentTypeFields as $contentTypeField) {
            $fieldType = FieldType::factory($contentTypeField, null, $locale);
            $data[$contentTypeField->short_tag] = $fieldType->render();
        }

        $data['title'] = $this->title;

        return $data;
    }

    /**
     * Returns a content type builder instance
     *
     * @param  array $parameters
     * @return \CmsCanvas\Content\Type\Builder\Type
     */
    public function newContentTypeBuilder($parameters = [])
    {
        return new ContentTypeBuilder($this, $parameters);
    }

    /**
     * Generates a view of the content type's layout
     *
     * @param  array $parameters
     * @return string
     */
    public function render($parameters = [])
    {
        return $this->newContentTypeBuilder($parameters)->render();
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
     * Returns a content type page from cache
     *
     * @return \CmsCanvas\Container\Cache\Page $cache
     */
    public function getCache()
    {
        if ($this->cache == null) {
            $contentType = $this;

            $this->cache = Cache::rememberForever($this->getRouteName(), function() use($contentType) {
                return new Page($contentType->id, 'contentType');
            });
        }

        return $this->cache;
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

        $dataItems = ( ! empty($entry)) ? $entry->allData : new Collection();

        $languages = Language::where('active', 1)
            ->orderBy('default', 'desc')
            ->orderBy('language', 'asc')
            ->get();
            
        $defaultLocale = Lang::getLocale();

        $fieldInstances = new FieldTypeCollection();

        foreach ($contentTypeFields as $contentTypeField) {
            $fieldDataItems = $dataItems->getWhere('content_type_field_id', $contentTypeField->id);

            if ($contentTypeField->translate) {
                foreach ($languages as $language) {
                    $dataItem = $fieldDataItems->getFirstWhere('locale', $language->locale);
                    $data = ($dataItem != null) ? $dataItem->data : '';
                    $metadata = ($dataItem != null) ? $dataItem->metadata : '';

                    $fieldInstances[] = FieldType::factory($contentTypeField, $entry, $language->locale, $data, $metadata);
                }
            } else {
                $dataItem = $fieldDataItems->getFirstWhere('locale', $defaultLocale);
                $data = ($dataItem != null) ? $dataItem->data : '';
                $metadata = ($dataItem != null) ? $dataItem->metadata : '';

                $fieldInstances[] = FieldType::factory($contentTypeField, $entry, $defaultLocale, $data, $metadata);
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
        ) {
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
        if ($this->adminEntryViewPermission != null) {
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

        if ($this->adminEntryEditPermission != null) {
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

        if ($this->adminEntryCreatePermission != null) {
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

        if ($this->adminEntryDeletePermission != null) {
            Auth::user()->checkPermission($this->adminEntryDeletePermission->key_name);
        }
    }

}
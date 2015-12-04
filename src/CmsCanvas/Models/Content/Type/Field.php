<?php 

namespace CmsCanvas\Models\Content\Type;

use CmsCanvas\Database\Eloquent\Model;
use CmsCanvas\Models\Content\Entry\Data as EntryData;

class Field extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'content_type_fields';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = [
        'content_type_field_type_id', 
        'label', 
        'short_tag', 
        'translate', 
        'required', 
        'settings',
        'options',
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
    protected static $sortable = ['id', 'label', 'updated_at'];

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
     * The entry associated with the field
     *
     * @var \CmsCanvas\Models\Content\Entry
     */
    protected $entry;

    /**
     * Content associated with the field
     *
     * @var string
     */
    protected $data;

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
     * Defines a one to many relationship with content type field types
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('\CmsCanvas\Models\Content\Type\Field\Type', 'content_type_field_type_id');
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::updated(function($field) {
            if ($field->isDirty('short_tag')) {
                // Because the entry_data table is a wee bit denormalized update all of the cooresponding
                // content_type_field_id rows with the new short_tag if it has changed.
                EntryData::where('content_type_field_id', $field->id)
                    ->update(['content_type_field_short_tag' => $field->short_tag]);
            }
        });
    }

}
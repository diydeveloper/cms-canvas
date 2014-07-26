<?php namespace CmsCanvas\Models\Content\Type;

use CmsCanvas\Database\Eloquent\Model;

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
    protected $fillable = array(
        'content_type_field_type_id', 
        'label', 
        'short_tag', 
        'translate', 
        'required', 
        'settings'
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
    protected static $sortable = array('id', 'label', 'updated_at');

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

}
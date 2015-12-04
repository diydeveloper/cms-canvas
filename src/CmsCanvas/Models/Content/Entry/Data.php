<?php 

namespace CmsCanvas\Models\Content\Entry;

use CmsCanvas\Database\Eloquent\Model;

class Data extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'entry_data';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = [
        'entry_id', 
        'content_type_field_id', 
        'content_type_field_short_tag', 
        'language_id', 
        'language_locale', 
        'data', 
        'metadata'
    ];

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Defines a one to many relationship with entries
     *
     * @return BelongsTo
     */
    public function entry()
    {
        return $this->belongsTo('\CmsCanvas\Models\Content\Entry', 'entry_id');
    }

    /**
     * Defines a one to many relationship with entries
     *
     * @return BelongsTo
     */
    public function contentTypeField()
    {
        return $this->belongsTo('\CmsCanvas\Models\Content\Type\Field', 'content_type_field_id');
    }

}
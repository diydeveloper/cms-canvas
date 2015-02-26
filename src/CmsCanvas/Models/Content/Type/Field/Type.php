<?php namespace CmsCanvas\Models\Content\Type\Field;

use CmsCanvas\Database\Eloquent\Model;

class Type extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'content_type_field_types';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = array('name', 'key_name');

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = array('id', 'created_at', 'updated_at');

    /**
     * Defines a one to many relationship with content type fields
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contentTypeFields()
    {
        return $this->hasMany('\CmsCanvas\Models\Content\Type\Field', 'content_type_field_type_id');
    }

}
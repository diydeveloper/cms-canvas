<?php 

namespace CmsCanvas\Models\Content\Type;

use CmsCanvas\Database\Eloquent\Model;

class MediaType extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'content_type_media_types';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = ['name', 'mime_type'];

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Defines a one to many relationship with content types
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contentTypes()
    {
        return $this->hasMany('\CmsCanvas\Models\Content\Type', 'media_type_id');
    }

}
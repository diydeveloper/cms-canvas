<?php namespace CmsCanvas\Models\Content;

use CmsCanvas\Database\Eloquent\Model;

class Revision extends Model {

    /**
     * @var int
     */
    const ENTRY_RESOURCE_TYPE_ID = 1;

    /**
     * @var int
     */
    const CONTENT_TYPE_RESOURCE_TYPE_ID = 2;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'revisions';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = array(
        'resource_type_id', 
        'resource_id', 
        'content_type_id', 
        'author_id', 
        'author_name', 
        'data',
    );

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = array('id', 'created_at', 'updated_at');

}
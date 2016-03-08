<?php 

namespace CmsCanvas\Models\Content\Navigation\Item;

use CmsCanvas\Database\Eloquent\Model;

class Data extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'navigation_item_data';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = [
        'navigation_item_id', 
        'language_locale', 
        'link_text', 
    ];

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Defines a one to many relationship with navigation items
     *
     * @return BelongsTo
     */
    public function item()
    {
        return $this->belongsTo('\CmsCanvas\Models\Content\Navigation\Item', 'navigation_item_id');
    }

}
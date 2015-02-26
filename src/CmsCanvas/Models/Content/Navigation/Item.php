<?php namespace CmsCanvas\Models\Content\Navigation;

use CmsCanvas\Database\Eloquent\Model;

class Item extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'navigation_items';

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = array('title');

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = array('id', 'created_at', 'updated_at');

    /**
     * Defines a one to many relationship with navigation items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('CmsCanvas\Models\Content\Navigation\Item', 'parent_id', 'id');
    }

    /**
     * Defines a one to one relationship with navigation items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parent()
    {
        return $this->hasOne('CmsCanvas\Models\Content\Navigation\Item', 'id', 'parent_id');
    }

}
<?php namespace CmsCanvas\Models\Content\Entry;

use CmsCanvas\Database\Eloquent\Model;

class Status extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'entry_statuses';

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
     * Defines a one to many relationship with entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries()
    {
        return $this->hasMany('\CmsCanvas\Models\Content\Entry', 'entry_status_id');
    }

}
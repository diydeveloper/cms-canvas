<?php namespace CmsCanvas\Models;

use Content, Theme;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use CmsCanvas\Database\Eloquent\Model;

class User extends Model implements UserInterface, RemindableInterface {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = array(
        'user_group_id',
        'timezone_id',
        'first_name', 
        'last_name', 
        'email', 
        'active',
        'phone',
        'address',
        'address2',
        'city',
        'state',
        'country',
        'zip',
    );

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = array('id', 'password', 'last_login', 'created_at', 'updated_at');

    /**
     * The columns that can sorted with the query builder orderBy method.
     *
     * @var array
     */
    protected static $sortable = array(
        'first_name', 
        'last_name', 
        'email', 
        'group_name', // Requires group to be joined to sort
        'last_login'
    );

    /**
     * The column to sort by if no session order by is defined.
     *
     * @var string
     */
    protected static $defaultSortColumn = 'last_name';

    /**
     * Defines a one to many relationship with groups
     *
     * @return BelongsTo
     */
    public function group()
    {
        return $this->belongsTo('\CmsCanvas\Models\User\Group', 'user_group_id');
    }

    /**
     * Defines a one to many relationship with timezones
     *
     * @return BelongsTo
     */
    public function timezone()
    {
        return $this->belongsTo('\CmsCanvas\Models\Timezone', 'timezone_id');
    }

    /**
     * Combines the user's first and last name
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
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
        if ( isset($filter->search) && $filter->search != '')
        {
            $query->whereRaw("(concat_ws(' ', first_name, last_name) LIKE '%{$filter->search}%' OR email LIKE '%{$filter->search}%')");
        }

        if ( ! empty($filter->user_group_id)) {
            $query->where('user_group_id', $filter->user_group_id); 
        }

        return $query;
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
        if (in_array($orderBy->getColumn(), self::$sortable))
        {
            $query->orderBy($orderBy->getColumn(), $orderBy->getSort()); 
        }

        return $query;
    }

    /**
     * Returns a collection of users who can author entries
     *
     * @return \CmsCanvas\Models\User|collection
     */
    public static function getAuthors()
    {
        return self::orderBy('first_name', 'asc')->get();
    }

    /**
     * Returns date fields as a carbon instance
     *
     * @return array
     */
    public function getDates()
    {
        return array('created_at', 'updated_at', 'last_login');
    }

    /**
     * Returns a portrait image URL of the user
     *
     * @param int $width
     * @param int $height
     * @param bool $crop
     * @return string
     */
    public function portrait($width = null, $height = null, $crop = false)
    {
        return Content::thumbnail('', $width, $height, $crop, array('noImage' => Theme::asset('images/portrait.jpg')));
    }

    /**
     * Returns the user's timezone identifier
     *
     * @return string
     */
    public function getTimezoneIdentifier()
    {
        return $this->timezone->identifier;
    }

}

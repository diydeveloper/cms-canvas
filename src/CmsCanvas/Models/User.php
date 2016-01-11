<?php 

namespace CmsCanvas\Models;

use Content, Theme, Session, Cache, Auth;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use CmsCanvas\Database\Eloquent\Model;
use CmsCanvas\User\Render;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
    
    use Authenticatable, CanResetPassword;

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
    protected $hidden = ['password'];

    /**
     * The columns that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = [
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
    ];

    /**
     * The columns that can NOT be mass-assigned.
     *
     * @var array
     */
    protected $guarded = ['id', 'password', 'last_login', 'created_at', 'updated_at'];

    /**
     * The columns that can sorted with the query builder orderBy method.
     *
     * @var array
     */
    protected static $sortable = [
        'first_name', 
        'last_name', 
        'email', 
        'last_login'
    ];

    /**
     * The column to sort by if no session order by is defined.
     *
     * @var string
     */
    protected static $defaultSortColumn = 'last_name';

    /**
     * Defines a many to many relationship with roles
     *
     * @return HasMany
     */
    public function roles()
    {
        return $this->belongsToMany('\CmsCanvas\Models\Role', 'user_roles', 'user_id', 'role_id');
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
        if ( isset($filter->search) && $filter->search != '') {
            $query->whereRaw("(concat_ws(' ', first_name, last_name) LIKE '%{$filter->search}%' OR email LIKE '%{$filter->search}%')");
        }

        if (! empty($filter->role_id)) {
            $query->whereHas('roles', function($query) use($filter) {
                $query->where('roles.id', $filter->role_id);
            });
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
        if (in_array($orderBy->getColumn(), self::$sortable)) {
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
        return ['created_at', 'updated_at', 'last_login'];
    }

    /**
     * Returns a portrait image URL of the user
     *
     * @param  int  $width
     * @param  int  $height
     * @param  bool $crop
     * @return string
     */
    public function portrait($width = null, $height = null, $crop = false)
    {
        return Content::thumbnail(
            '', 
            [
                'width' => $width, 
                'height' => $height, 
                'crop' => $crop, 
                'no_image' => Theme::asset('images/portrait.jpg')
            ]
        );
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

    /**
     * Check if the user is assigned to the specified role
     *
     * @param  string  $name
     * @return bool
     */
    public function hasRole($name)
    {
        $this->loadRolesFromSession();

        foreach ($this->roles as $role) {
            if ($role->name == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has a permission.
     *
     * @param  string  $permission
     * @return bool
     */
    public function can($keyName)
    {
        $this->loadRolesFromSession();

        foreach ($this->roles as $role) {
            if ($role->hasPermission($keyName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Abort with HTTP 403 if user does not have permission 
     *
     * @param  string  $permission
     * @return void
     */
    public function checkPermission($permission)
    {
        if (! $this->can($permission)) {
            throw new \CmsCanvas\Exceptions\PermissionDenied($permission);
        }
    }

    /**
     * Loads the authenticated user's roles from cache using
     * role ids set in the session.
     *
     * @return void
     */
    protected function loadRolesFromSession()
    {
        if (isset($this->relations['roles']) || Auth::user()->id != $this->id) {
            return;
        }

        $roleIds = Session::get('role_ids');

        if (is_array($roleIds)) {
            $roles = $this->newCollection();

            foreach ($roleIds as $roleId) {
                $role = Cache::rememberForever('role_'.$roleId, function() use($roleId) {
                    return Role::with('permissions')->find($roleId);
                });

                if ($role != null) {
                    $roles[] = $role;
                }
            }
        } else {
            $roles = $this->roles()->with('permissions')->get();

            foreach ($roles as $role) {
                Cache::forever('role_'.$role->id, $role);
            }

            Session::put('role_ids', $roles->lists('id')->all());
        }

        $this->setRelation('roles', $roles);
    }

    /**
     * Returns a render instance
     *
     * @return \CmsCanvas\User\Render
     */
    public function render()
    {
        return new Render($this);
    }

}

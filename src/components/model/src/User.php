<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Model;

use Antares\Contracts\Comments\Commentable;
use Antares\Contracts\Tags\Taggable;
use Antares\Logger\Model\Logs;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Antares\Notifier\NotifiableTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Antares\Support\Traits\QueryFilterTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Antares\Contracts\Notification\Recipient;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Antares\Logger\Traits\LogRecorder;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;

/**
 * Class User
 * @property int $id
 * @property string $email
 * @property string $fullname
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class User extends Eloquent implements UserContract, CanResetPasswordContract, Recipient, Taggable, Commentable, AuthorizableContract
{

    use Authenticatable,
        Authorizable,
        NotifiableTrait,
        QueryFilterTrait,
        CanResetPassword,
        SoftDeletes,
        LogRecorder;

    // Disables the log record in this model.
    protected $auditEnabled   = true;
    // Disables the log record after 500 records.
    protected $historyLimit   = 500;
    // Fields you do NOT want to register.
    protected $dontKeepLogOf  = ['created_at', 'updated_at'];
    // Tell what actions you want to audit.
    protected $auditableTypes = ['created', 'saved', 'deleted'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_users';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'User';

    /**
     * Available user status as constant.
     */
    const UNVERIFIED = 0;
    const SUSPENDED  = 63;
    const VERIFIED   = 1;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be filled.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'status',
        'email',
        'created_at',
        'updated_at',
        'password',
        'remember_token',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = array('fullname');

    /**
     * Has many and belongs to relationship with Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('\Antares\Model\Role', 'tbl_user_role')->withTimestamps();
    }

    /**
     * clients scoping
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeClients(Builder $query)
    {
        $query->with('roles')->whereNotNull('tbl_users.id')->whereHas('roles', function ($query) {
            $query->whereIn('tbl_roles.name', ['member', 'users', 'user']);
        });


        return $query;
    }

    /**
     * administrators scoping
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeAdministrators(Builder $query)
    {
        $query->with('roles')->whereNotNull('tbl_users.id')->whereHas('roles', function ($query) {
            $query->whereNotIn('tbl_roles.name', ['member', 'guest']);
        });


        return $query;
    }

    /**
     * administrators scoping
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeResellers(Builder $query)
    {
        $query->with('roles')->whereNotNull('tbl_users.id')->whereHas('roles', function ($query) {
            $query->whereIn('tbl_roles.name', ['reseller']);
        });


        return $query;
    }

    /**
     * clients scoping
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeMembers(Builder $query)
    {
        $query->with('roles')->whereNotNull('tbl_users.id')->whereHas('roles', function ($query) {
            $query->whereIn('tbl_roles.name', ['member']);
        });


        return $query;
    }

    /**
     * role scope
     *
     * @param Builder $query
     * @param array $roles
     * @return Builder
     */
    public function scopeRole(Builder $query, $roles = [])
    {
        $query->with('roles')->whereNotNull('tbl_users.id');

        if (!empty($roles)) {
            $query->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('tbl_roles.name', $roles);
            });
        }

        return $query;
    }

    /**
     * Area scope
     *
     * @param Builder $query
     * @param mixed $areas
     * @return Builder
     */
    public function scopeArea(Builder $query, $areas)
    {
        $query->with('roles')->whereNotNull('tbl_users.id');
        if (empty($areas)) {
            return $query;
        }
        $query->whereHas('roles', function ($query) use ($areas) {
            $query->whereIn('tbl_roles.area', (array) $areas);
        });

        return $query;
    }

    public function getArea()
    {
        $areas = $this->roles->pluck('area')->toArray();
        return count($areas) > 1 ? $areas : current($areas);
    }

    /**
     * get fields with relation to user
     * @return \Illuminate\Support\Collection
     */
    public function fields()
    {
        return $this->hasMany('\Antares\Customfields\Model\FieldData', 'user_id', 'id');
    }

    /**
     * get user activity
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function activity()
    {
        return $this->hasOne(UserActivity::class, 'user_id', 'id');
    }

    /**
     * Check wheter this user is currently active
     * @return bool
     */
    public function getIsActiveAttribute()
    {
        try {
            $activity     = $this->activity;
            $lastActivity = ($activity instanceof UserActivity) ? $activity->last_activity : null;
            $laDate       = Carbon::createFromFormat('Y-m-d H:i:s', $lastActivity);
            return !(Carbon::now()->diffInSeconds($laDate) >= config('antares/users::check_activity_every'));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getIsAfkAttribute()
    {
        try {
            $activity     = $this->activity;
            $lastActivity = ($activity instanceof UserActivity) ? $activity->last_activity : null;
            $laDate       = Carbon::createFromFormat('Y-m-d H:i:s', $lastActivity);
            $inSeconds    = Carbon::now()->diffInSeconds($laDate);
            return ($inSeconds > config('antares/users::check_activity_every') && Carbon::now()->diffInSeconds($laDate) < 900);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get last dateTime of last activity of this user
     * @return null|Carbon
     */
    public function getLastActivityAttribute()
    {
        $activity = $this->activity;
        return ($activity instanceof UserActivity) ?
                Carbon::createFromFormat('Y-m-d H:i:s', $activity->last_activity) : null;
    }

    /**
     * Set `password` mutator.
     *
     * @param  string $value
     *
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Get the e-mail address where notification are sent.
     *
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->getEmailForPasswordReset();
    }

    /**
     * Get the fullname where notification are sent.
     *
     * @return string
     */
    public function getRecipientName()
    {
        return $this->getAttribute('fullname');
    }

    /**
     * Get roles name as an array.
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = (array_key_exists('roles', $this->relations) ? $this->relations['roles'] : $this->roles());
        return $roles->pluck('name');
    }

    /**
     * Activate current user.
     *
     * @return \Antares\Model\User
     */
    public function activate()
    {
        $this->setAttribute('status', self::VERIFIED);

        return $this;
    }

    /**
     * Assign role to user.
     *
     * @param  int|array $roles
     *
     * @return void
     */
    public function attachRole($roles)
    {
        $this->roles()->sync((array) $roles, false);
    }

    /**
     * Deactivate current user.
     *
     * @return \Antares\Model\User
     */
    public function deactivate()
    {
        $this->setAttribute('status', self::UNVERIFIED);

        return $this;
    }

    /**
     * Un-assign role from user.
     *
     * @param  int|array $roles
     *
     * @return void
     */
    public function detachRole($roles)
    {
        $this->roles()->detach((array) $roles);
    }

    /**
     * Determine if current user has the given role.
     *
     * @param  string $roles
     *
     * @return bool
     */
    public function hasRoles($roles)
    {
        $userRoles = $this->getRoles()->toArray();

        if (!is_array($userRoles)) {
            return false;
        }

        foreach ((array) $roles as $role) {
            if (!in_array($role, $userRoles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if the current user account activated or not.
     *
     * @return bool
     */
    public function isActivated()
    {
        return (int) $this->getAttribute('status') === self::VERIFIED;
    }

    /**
     * Determine if current user has any of the given role.
     *
     * @param  array $roles
     *
     * @return bool
     */
    public function isAny(array $roles)
    {
        $userRoles = $this->getRoles()->toArray();

        if (!is_array($userRoles)) {
            return false;
        }

        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if current user does not has any of the given role.
     *
     * @param  string $roles
     *
     * @return bool
     */
    public function isNotAn($roles)
    {
        return !$this->hasRoles($roles);
    }

    /**
     * Determine if current user does not has any of the given role.
     *
     * @param  array $roles
     *
     * @return bool
     */
    public function isNotAny(array $roles)
    {
        return !$this->isAny($roles);
    }

    /**
     * Determine if the current user account suspended or not.
     *
     * @return bool
     */
    public function isSuspended()
    {
        return (int) $this->getAttribute('status') === self::SUSPENDED;
    }

    /**
     * Send notification for a user.
     *
     * @param  \Antares\Contracts\Notification\Message|string $subject
     * @param  string|array|null $view
     * @param  array $data
     *
     * @return \Antares\Contracts\Notification\Receipt
     */
    public function notify($subject, $view = null, array $data = [])
    {
        return $this->sendNotification($this, $subject, $view, $data);
    }

    /**
     * Suspend current user.
     *
     * @return \Antares\Model\User
     */
    public function suspend()
    {
        $this->setAttribute('status', (int) self::SUSPENDED);
        return $this;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|UserMeta[]
     */
    public function meta()
    {
        return $this->hasMany(UserMeta::class, 'user_id', 'id');
    }

    /**
     * Get the user's fullname
     *
     * @param  string $value
     * @return string
     */
    public function getFullnameAttribute($value)
    {
        if (!strlen($this->firstname) and ! strlen($this->lastname)) {
            return '---';
        }
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Gets patterned url for search engines
     *
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::users/{id}/edit');
    }

    /**
     * Getting last logged attribute
     *
     * @return String
     */
    public function getLastLoggedAtAttribute()
    {
        $lastLogin = Logs::where('user_id', $this->id)
                ->where('name', 'like', 'USERAUTHLISTENER_ONUSERLOGIN')
                ->orderBy('created_at', 'desc')
                ->get(['created_at'])
                ->first();

        return ($lastLogin) ? $lastLogin->created_at->diffForHumans() : 'never';
    }

    public function getPhoneAttribute()
    {
        return '697274132';
    }

}

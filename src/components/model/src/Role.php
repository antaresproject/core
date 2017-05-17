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

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Antares\Logger\Traits\LogRecorder;

class Role extends Eloquent
{

    use SoftDeletes,
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
    protected $table = 'tbl_roles';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'Role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'area', 'name', 'full_name', 'description'];

    /**
     * Default roles.
     *
     * @var array
     */
    protected static $defaultRoles = [
        'admin'  => 2,
        'member' => 4,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Set default roles.
     *
     * @param  array  $roles
     *
     * @return void
     */
    public static function setDefaultRoles(array $roles)
    {
        static::$defaultRoles = array_merge(static::$defaultRoles, $roles);
    }

    /**
     * Has many and belongs to relationship with User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('\Antares\Model\User', 'tbl_user_role')->withTimestamps();
    }

    /**
     * Get default roles for Antares.
     *
     * @return $this|null
     */
    public static function admin()
    {
        return static::query()->where('name', '=', 'super-administrator')->orWhere('name', '=', 'administrator')->firstOrFail();
    }

    /**
     * Get only manager roles
     *
     * @return $this|null
     */
    public static function managers()
    {
        return static::query()->whereNotIn('name', ['guest', 'member']);
    }

    /**
     * Get only members roles
     *
     * @return $this|null
     */
    public static function members()
    {
        return static::query()->whereIn('name', ['member']);
    }

    /**
     * Get default member roles for Antares.
     *
     * @return $this|null
     */
    public static function member()
    {
        return static::query()->find(static::$defaultRoles['member']);
    }

    /**
     * Get default reseller roles for Antares.
     *
     * @return $this|null
     */
    public static function reseller()
    {
        return static::whereRaw('id=(select parent_id from tbl_roles where name in ("member","user"))')->firstOrFail();
    }

    /**
     * scope only authorized roles
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeAuthorized(Builder $query)
    {
        return $query->whereNotIn('name', ['guest']);
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::control/roles/{id}/edit');
    }

    /**
     * builds recursive widgets stack
     * 
     * @param array $elements
     * @param mixed $parentId
     * @return array
     */
    protected function getLowerRoles(array $elements, $parentId = 0, &$return = [])
    {
        foreach ($elements as $element) {
            if ($element['parent_id'] != $parentId) {
                continue;
            }
            $children = $this->getLowerRoles($elements, $element['id'], $return);
            if ($children) {
                foreach ($children as $child) {
                    $return[] = $child['id'];
                }
            }
            $return[] = $element['id'];
        }

        return array_filter($return);
    }

    /**
     * Gets child roles
     * 
     * @return array
     */
    public function getChilds()
    {
        $id    = $this->id;
        $roles = $this->withTrashed()->orderby('parent_id')->get()->toArray();
        return $this->getLowerRoles($roles, $id);
    }

}

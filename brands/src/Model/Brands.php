<?php

/**
 * Part of the Antares Project package.
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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Brands\Model;

use Antares\Logger\Traits\LogRecorder;
use Antares\Model\Eloquent;

class Brands extends Eloquent
{

    use LogRecorder;

    // Disables the log record in this model.
    protected $auditEnabled   = true;
    // Disables the log record after 500 records.
    protected $historyLimit   = 500;
    // Fields you do NOT want to register.
    protected $dontKeepLogOf  = ['created_at', 'updated_at', 'deleted_at'];
    // Tell what actions you want to audit.
    protected $auditableTypes = ['created', 'saved', 'deleted'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table      = 'tbl_brands';
    protected $casts      = [
        'id'      => 'integer',
        'status'  => 'boolean',
        'default' => 'boolean',
    ];
    protected $attributes = [
        'status'  => false,
        'default' => false,
    ];
    protected $fillable   = ['name', 'description', 'status', 'default'];

    /**
     * Query scope for latest by specified field.
     *
     * @param  object       $query
     * @param  string|null  $orderBy
     * @param  int|null     $take
     *
     * @return void
     */
    public function scopeLatestBy($query, $orderBy = null, $take = null)
    {
        if (is_null($orderBy)) {
            $orderBy = static::CREATED_AT;
        }

        if (is_int($take) and $take > 0) {
            $query->take($take);
        }

        $query->orderBy($orderBy, 'DESC');
    }

    /**
     * Query scope for latest published.
     *
     * @param  object     $query
     * @param  int|null   $take
     *
     * @return void
     */
    public function scopeLatest($query, $take = null)
    {
        if (is_int($take) and $take > 0) {
            $query->take($take);
        }

        $query->latestBy(static::CREATED_AT, 'DESC');
    }

    public function scopeDefault($query)
    {
        $query->where('default', 1)->with(['options', 'templates' => function($query) {
                if (!is_null($area = area())) {
                    $query->where('area', $area);
                }
            }]);
    }

    /**
     * @return default brand
     */
    public static function defaultBrand()
    {
        return static::query()->select()->where('default', '=', 1)->first();
    }

    /**
     * Gets brand model by id
     * 
     * @param int $id
     * @return Eloquent
     */
    public static function brand($id)
    {
        return static::query()->where('id', '=', $id)->first();
    }

    /**
     * Relation to permissions
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany('Antares\Model\Permission', 'brand_id');
    }

    /**
     * Whether brand is default
     * 
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * Whether brand is active
     * 
     * @return boolean
     */
    public function isActive()
    {
        return $this->status;
    }

    /**
     * relation to brand options
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function options()
    {
        return $this->hasOne(BrandOptions::class, 'brand_id');
    }

    /**
     * relation to brand options
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function templates()
    {
        return $this->hasMany(BrandTemplates::class, 'brand_id');
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::brands/{id}/edit');
    }

}

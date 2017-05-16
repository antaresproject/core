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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Model;

use Antares\Model\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Antares\Logger\Traits\LogRecorder;

class FieldCategory extends Eloquent
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
    public $timestamps        = false;
    public $fillable          = ['name', 'description'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_fields_categories';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'FieldCategory';

    /**
     * get default category
     * @return self
     */
    public static function getDefault()
    {
        return static::query()->first();
    }

    /**
     * Belongs to relationship with Group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->hasMany('Antares\Customfields\Model\FieldGroup', 'category_id', 'id');
    }

    /**
     * Return a meta data belong to a user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $name
     * @param  int  $userId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch(Builder $query, $name)
    {
        return $query->where('name', '=', $name);
    }

    /**
     * Many Through relation to Fields
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function fields()
    {
        return $this->hasManyThrough(Field::class, FieldGroup::class, 'category_id', 'group_id');
    }

    /**
     * Gets url pattern for logs 
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::customfields/{name}/index');
    }

}

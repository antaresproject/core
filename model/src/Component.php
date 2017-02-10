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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Model;

use Illuminate\Database\Eloquent\Builder;

class Component extends Eloquent
{

    public $table = 'tbl_components';

    /**
     * @var array
     */
    protected $fillable = ['name', 'full_name', 'description', 'status', 'path', 'author', 'url', 'version', 'options'];

    /**
     * @see docs
     */
    public $timestamps = false;
    protected $casts = [
        'id'      => 'integer',
        'status'  => 'boolean',
        'order'   => 'integer',
        'options' => 'array',
    ];

    /**
     * 
     * @return type
     */
    public function actions()
    {
        return $this->hasMany('Antares\Model\Action', 'component_id');
    }

    public function config()
    {
        return $this->hasOne('Antares\Model\ComponentConfig', 'component_id');
    }

    /**
     * fetch one record by name column
     * @param String $name
     * @return Eloquent
     */
    public static function findOneByName($name)
    {
        $names = explode('/', $name);
        return static::query()->where('name', end($names))->get()->first();
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
    public function scopeAction(Builder $query, $name)
    {
        return $query->with('Antares\Model\Action')->where('name', $name);
    }

}

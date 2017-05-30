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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Model;

/**
 * Class Action
 * @package Antares\Model
 * @property int $id
 * @property int $component_id
 * @property string $name
 */
class Action extends Eloquent
{

    /**
     * name of table
     * String
     */
    public $table = 'tbl_actions';

    /**
     * has model any timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * fillable array of attributes
     *
     * @var array
     */
    protected $fillable = ['component_id', 'category_id', 'name', 'description'];

    /**
     * has one relation to extension model
     *
     * @return Component|\Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function extension()
    {
        return $this->hasOne(Component::class, 'id', 'component_id');
    }

    /**
     * Relation to action category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->hasOne(ActionCategories::class, 'id', 'category_id');
    }

}

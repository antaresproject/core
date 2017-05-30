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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Model;

class ActionCategories extends Eloquent
{

    /**
     * Name of table
     * 
     * String
     */
    public $table = 'tbl_action_categories';

    /**
     * Has model any timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Fillable array of attributes
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Relation to actions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function actions()
    {
        return $this->hasMany(Action::class, 'category_id', 'id');
    }

}

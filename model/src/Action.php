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
    protected $fillable = ['component_id', 'name'];

    /**
     * has one relation to extension model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function extension()
    {
        return $this->hasOne('Antares\Model\Component', 'id', 'component_id');
    }

}

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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Model;

use Antares\Model\Eloquent;

class Version extends Eloquent
{

    /**
     * tablename
     *
     * @var String
     */
    protected $table = 'tbl_version';

    /**
     * does the table has timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * which columns can be filled in safe mode
     *
     * @var array 
     */
    protected $fillable = array('description', 'changelog', 'path', 'db_version', 'app_version', 'last_update_date', 'next_update_date', 'is_actual');

    /**
     * search url pattern to redirect after search row click
     *
     * @var String
     */
    protected $searchUrlPattern = 'antares::updater';

    /**
     * columns with date types
     * 
     * @return array
     */
    public function getDates()
    {
        return array('last_update_date', 'next_update_date');
    }

    /**
     * get system actual version
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeActual($query)
    {
        return $query->where('is_actual', '=', 1);
    }

    /**
     * get system previous version
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopePrevious($query)
    {
        return $query->where('is_actual', '=', 0);
    }

}

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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Model;

use Antares\Model\Eloquent;

class LogPriorities extends Eloquent
{

    /**
     * Tablename
     *
     * @var String
     */
    protected $table = 'tbl_log_priorities';

    /**
     * Does table use timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Definition of fillable columns
     *
     * @var array
     */
    protected $fillable = array('num', 'name');

    /**
     * Relation to Logs table
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tblLogs()
    {
        return $this->hasMany('Antares\Logger\Model\Logs', 'priority_id', 'id');
    }

}

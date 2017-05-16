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

class LogTypes extends Eloquent
{

    /**
     * Tablename
     *
     * @var String 
     */
    protected $table = 'tbl_log_types';

    /**
     * Does table use timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Columns with timestamps
     * 
     * @return array
     */
    public function getDates()
    {
        return ['created_date'];
    }

    /**
     * Fillable columns
     *
     * @var array
     */
    protected $fillable = array('name', 'active', 'created_date');

    /**
     * Relation to LLogs
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany('Antares\Logger\Model\Logs', 'type_id', 'id');
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::notifications');
    }

}

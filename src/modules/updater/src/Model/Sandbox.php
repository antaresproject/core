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

use Antares\Logger\Traits\LogRecorder;
use Antares\Model\Eloquent;

class Sandbox extends Eloquent
{

    use LogRecorder;

    // Disables the log record in this model.
    protected $auditEnabled  = true;
    // Disables the log record after 500 records.
    protected $historyLimit  = 500;
    // Fields you do NOT want to register.
    protected $dontKeepLogOf = ['created_at'];
// Tell what actions you want to audit.
    protected $auditableTypes = ['created', 'saved', 'deleted'];

    /**
     * tablename
     *
     * @var String
     */
    protected $table = 'tbl_sandbox';

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
    protected $fillable = ['version', 'path', 'files', 'created_at'];

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::updater/sandboxes');
    }

}

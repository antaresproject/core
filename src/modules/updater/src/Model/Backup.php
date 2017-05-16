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

class Backup extends Eloquent
{

    /**
     * tablename
     *
     * @var String
     */
    protected $table = 'tbl_backup';

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
    protected $fillable = array('name', 'version', 'path', 'status', 'created_at');

    /**
     * search url pattern to redirect after search row click
     *
     * @var String
     */
    protected $searchUrlPattern = 'antares::updater/backups';

}

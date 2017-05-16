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

use Illuminate\Database\Eloquent\Model;

class Checksum extends Model
{

    /**
     * @var String
     */
    protected $table = 'tbl_checksum';

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * @var array 
     */
    protected $fillable = array('name', 'value');

}

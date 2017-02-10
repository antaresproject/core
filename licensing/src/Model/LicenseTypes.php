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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Licensing\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class LicenseTypes extends Eloquent
{

    /**
     * tablename
     *
     * @var String 
     */
    protected $table = 'tbl_license_types';

    /**
     * does the table uses timestamps
     *
     * @var String
     */
    public $timestamps = false;

    /**
     * fillable table columns
     *
     * @var array 
     */
    protected $fillable = array('name', 'clients_count', 'extensions');

    public function licenses()
    {
        return $this->hasMany('Antares\Licensing\Model\License', 'type_id', 'id');
    }

}

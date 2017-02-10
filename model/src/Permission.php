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

class Permission extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_permissions';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'Permission';

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * @var type 
     */
    protected $fillable = ['brand_id', 'component_id', 'role_id', 'action_id', 'allowed'];

    public function brands()
    {
        return $this->hasMany('Antares\Brands\Model\Brands', 'brand_id');
    }

}

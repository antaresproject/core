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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Model;

use Antares\Model\Eloquent;

class FieldData extends Eloquent
{

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_fields_data';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'FieldData';

    /**
     * fillable array
     * @var array
     */
    public $fillable = [
        'user_id', 'namespace', 'foreign_id', 'field_id', 'field_class', 'option_id', 'data'
    ];

    /**
     * has one relation to user table
     * @return Eloquent
     */
    public function user()
    {
        return $this->hasOne('\Antares\Model\User', 'id', 'user_id');
    }

    /**
     * has one relation to fields view
     * @return Eloquent
     */
    public function field()
    {
        return $this->hasOne('\Antares\Customfields\Model\FieldView', 'id', 'field_id');
    }

}

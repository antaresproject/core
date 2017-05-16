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

class FieldView extends Eloquent
{

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'view_fields';

    /**
     * The class name to be used in polymorphic relations.
     * @var string
     */
    protected $morphClass = 'FieldView';

    /**
     * one to many relation
     * @return \Illuminate\Support\Collection
     */
    public function config()
    {
        return $this->hasMany('Antares\Customfields\Model\FieldValidatorConfig', 'field_id', 'id');
    }

    /**
     * one to many relation to field data
     */
    public function data()
    {
        return $this->hasMany('Antares\Customfields\Model\FieldData', 'field_id', 'id');
    }

    /**
     * Relation to fieldsets
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fieldsets()
    {
        return $this->belongsToMany(Fieldsets::class, 'tbl_field_fieldsets', 'field_id', 'fieldset_id');
    }

    public function fieldFieldset()
    {
        return $this->hasOne(FieldFieldsets::class, 'field_id', 'id');
    }

}

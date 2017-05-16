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

class FieldTypeValidator extends Eloquent
{

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_fields_types_validators';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'FieldTypeValidator';

    /**
     * Belongs to relationship with Validator.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function validator()
    {
        return $this->hasOne('Antares\Customfields\Model\FieldValidator', 'id', 'validator_id');
    }

}

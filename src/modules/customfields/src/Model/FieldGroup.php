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

class FieldGroup extends Eloquent
{

    public $timestamps = false;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_fields_groups';

    /**
     * The class name to be used in polymorphic relations.
     *
     * @var string
     */
    protected $morphClass = 'FieldGroup';

    /**
     * fillable array
     * @var array
     */
    public $fillable = [
        'category_id', 'name'
    ];

    /**
     * Belongs to relationship with Validator.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->hasOne('Antares\Customfields\Model\FieldCategory', 'id', 'category_id');
    }

    /**
     * Gets url pattern for logs
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::customfields/index');
    }

}

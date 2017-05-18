<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Html\Model;

use Illuminate\Database\Eloquent\Model;

class FormConfig extends Model
{

    /**
     * tablename
     * 
     * @var String
     */
    protected $table = 'tbl_forms_config';

    /**
     * has timestamps
     * 
     * @var String
     */
    public $timestamps = false;

    /**
     * can be updated|inserted 
     * 
     * @var array 
     */
    protected $fillable = array('form_id', 'role_id', 'brand_id', 'value');

    /**
     * Belongs to relationship with Validator.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form()
    {
        return $this->hasOne('Antares\Form\Model\Form', 'id', 'form_id');
    }

}

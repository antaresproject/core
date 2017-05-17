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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Brands\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class BrandTemplates extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_brand_templates';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'colors' => 'array',
    ];

    /**
     * fillable attributes
     *
     * @var array 
     */
    protected $fillable = ['brand_id', 'area', 'composition', 'styleset', 'logo', 'favicon', 'colors'];

    /**
     * whether table has times columns
     * 
     * @var boolean
     */
    public $timestamps = false;

    /**
     * belongs to relation to brands table
     * 
     * @return Eloquent
     */
    public function brand()
    {
        return $this->belongsTo(Brands::class, 'id', 'brand_id');
    }

}

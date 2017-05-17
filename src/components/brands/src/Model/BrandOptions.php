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

use Antares\Translations\Models\Languages;
use Illuminate\Database\Eloquent\Model as Eloquent;

class BrandOptions extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_brand_options';

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
    protected $fillable = ['country_id', 'language_id', 'date_format_id', 'maintenance', 'url', 'header', 'styles', 'footer'];

    /**
     * whether table has times columns
     * 
     * @var boolean
     */
    public $timestamps = false;

    /**
     * belongs to relation to language table
     * 
     * @return Eloquent
     */
    public function language()
    {
        return $this->belongsTo(Languages::class, 'language_id');
    }

    /**
     * belongs to relation to country table
     * 
     * @return Eloquent
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * belongs to relation to brands table
     * 
     * @return Eloquent
     */
    public function brand()
    {
        return $this->belongsTo(Brands::class, 'id', 'brand_id');
    }

    /**
     * relation to date formats
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dateFormats()
    {
        return $this->belongsTo(DateFormat::class, 'date_format_id');
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl($id = null)
    {
        return is_null($id) ? null : handles("antares::brands/{$id}/edit");
    }

}

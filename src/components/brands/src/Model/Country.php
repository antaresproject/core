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
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Country
 * @package Antares\Brands\Model
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|Region[] $regions
 */
class Country extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_country';

    /**
     * fillable attributes
     *
     * @var array 
     */
    protected $fillable = ['code', 'name'];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * @return HasMany
     */
    public function regions() : HasMany {
        return $this->hasMany(Region::class);
    }

    /**
     * @param $value
     * @return string
     */
    public function getCodeAttribute($value) {
        return strtolower($value);
    }

}

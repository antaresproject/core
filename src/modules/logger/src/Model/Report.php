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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Model;

use Antares\Model\Eloquent;
use Antares\Logger\Traits\LogRecorder;

class Report extends Eloquent
{

    use LogRecorder;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tbl_reports';

    /**
     * The attributes that should be filled.
     *
     * @var array
     */
    protected $fillable = ['name', 'html', 'user_id', 'brand_id', 'type_id'];

    /**
     * relation to brand
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo('Antares\Brands\Model\Brands', 'brand_id', 'id');
    }

    /**
     * relation to type
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('Antares\Logger\Model\ReportType', 'type_id', 'id');
    }

    /**
     * relation to user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Antares\Model\User', 'user_id', 'id');
    }

    /**
     * Query scope for actual brand.
     *
     * @param  object     $query
     *
     * @return void
     */
    public function scopeCurrentBrand($query)
    {
        $query->where('brand_id', app('antares.memory')->make('primary')->get('brand.default'));
    }

}

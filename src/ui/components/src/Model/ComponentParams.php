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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Model;

use Illuminate\Database\Eloquent\Model;

class ComponentParams extends Model
{

    /**
     * @var String
     */
    protected $table = 'tbl_widgets_params';

    /**
     * @var array
     */
    protected $fillable = array('wid', 'uid', 'name', 'brand_id', 'resource', 'data');

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * has timestamps
     * 
     * @var String
     */
    public $timestamps = false;

    /**
     * one to one relation
     *
     * @return OneToOneRelation
     */
    public function widget()
    {
        return $this->belongsTo(Components::class, 'wid', 'id');
    }

    /**
     * {@inherited}
     */
    public function save(array $options = [])
    {
        $ignored    = config('antares/ui-components::defaults.ignored');
        $this->data = array_except($this->data, $ignored);
        return parent::save();
    }

}

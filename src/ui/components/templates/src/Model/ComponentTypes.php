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

use Antares\UI\UIComponents\Model\Components;
use Illuminate\Database\Eloquent\Model;

class ComponentTypes extends Model
{

    /**
     * Tablename
     * 
     * @var String
     */
    protected $table = 'tbl_widget_types';

    /**
     * Has timestamps
     * 
     * @var String
     */
    public $timestamps = false;

    /**
     * Can be updated|inserted 
     * 
     * @var array 
     */
    protected $fillable = array('name', 'slug', 'description');

    /**
     * relation one to many
     * 
     * @return Relation
     */
    public function widgets()
    {
        return $this->hasMany(Components::class, 'id', 'type_id');
    }

    /**
     * gets default ui component type
     * 
     * @return type
     */
    public function getDefault()
    {
        return $this->query()->where('slug', 'default')->firstOrFail();
    }

}

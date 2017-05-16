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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Model;

use Illuminate\Database\Eloquent\Model;

class MemoryTests extends Model
{

    /**
     * @var String
     */
    protected $table = 'tbl_memory_tests';

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * @var array 
     */
    protected $fillable = array('name', 'value');

    /**
     * relation to component
     * 
     * @return BelongsTo
     */
    public function components()
    {
        return $this->belongsTo('\Antares\Model\Component', 'component_id', 'id');
    }

    /**
     * saves test properties
     * 
     * @param array $options
     * @return numeric
     */
    public function save(array $options = array())
    {
        if (empty($options)) {
            $data               = unserialize($this->value);
            $this->component_id = $data['component_id'];
        }
        return parent::save($options);
    }

}

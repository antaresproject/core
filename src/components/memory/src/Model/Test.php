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


namespace Antares\Memory\Model;

use Antares\Model\Eloquent;

class Test extends Eloquent
{

    /**
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The database table used by the model.
     * @var string
     */
    protected $table = 'tbl_antares_options';

    /**
     * The class name to be used in polymorphic relations.
     * @var string
     */
    protected $morphClass = 'Test';

    /**
     * 
     * @var type 
     */
    public $fillable = [
        'name', 'value'
    ];

    public function save(array $options = array())
    {
        if (is_array($this->value)) {
            $this->value = last($this->value);
        }
        parent::save($options);
    }

}

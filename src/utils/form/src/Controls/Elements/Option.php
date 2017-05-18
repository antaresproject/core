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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Controls\Elements;

use Antares\Form\Contracts\Attributable;
use Antares\Form\Traits\AttributesTrait;

/**
 * @author Marcin Domański <marcin@domanskim.pl>
 * Date: 27.03.17
 * Time: 12:38
 */
class Option implements Attributable
{

    use AttributesTrait;

    /** @var mixed */
    public $value;
    
    /** @var string */
    public $name;
    
    /** @var bool */
    public $selected;

    /**
     * Option constructor.
     *
     * @param $value
     * @param $name
     * @param $attributes
     * @param $selected
     */
    public function __construct($value, $name, $attributes = [], $selected = false)
    {
        $this->value      = $value;
        $this->name       = $name;
        $this->attributes = $attributes;
        $this->selected   = $selected;
    }

}

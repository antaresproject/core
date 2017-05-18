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
 * @author         Mariusz Jucha <mariuszjucha@gmail.com>
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Controls\Elements;

class OptGroup
{

    /** @var string */
    public $label;
    
    /** @var array|\Traversable */
    public $options;

    /**
     * OptGroup constructor
     *
     * @param string $label
     * @param array|\Traversable $options
     */
    public function __construct($label, $options)
    {
        $this->label = $label;
        $this->options = $options;
    }

}

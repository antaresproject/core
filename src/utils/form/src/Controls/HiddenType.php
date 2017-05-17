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

namespace Antares\Form\Controls;

use Antares\Form\Decorators\AbstractDecorator;
use Antares\Form\Decorators\HiddenDecorator;

class HiddenType extends AbstractType
{

    /** @var string */
    protected $type = 'hidden';

	/**
	 * @return string
	 */
    public function render()
    {
        if (!$this->decorator instanceof AbstractDecorator) {
            $this->setDecorator((new HiddenDecorator()));
        }

        return parent::render();
    }

}

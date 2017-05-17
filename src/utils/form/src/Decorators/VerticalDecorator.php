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

namespace Antares\Form\Decorators;

use Antares\Form\Controls\AbstractType;

class VerticalDecorator extends AbstractDecorator
{

	/** @var string */
	protected $name = 'vertical';

	/**
	 * @param AbstractType $control
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function render(AbstractType $control)
	{
		$this->labelWrapper['class'] = 'child-align-top col-16 mb2';
		$this->inputWrapper['class'] = 'form-block col-dt-16 col-16 col-mb-16';

		return parent::render($control);
	}
    
}

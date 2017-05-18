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

class ButtonType extends AbstractType
{

	/** @var string */
	protected $type = 'button';

	protected $buttonType = 'button';

	const BUTTON_BUTTON = 'button';
	const BUTTON_SUBMIT = 'submit';
	const BUTTON_RESET = 'reset';

	/**
	 * @param string $buttonType
	 * @return ButtonType
	 */
	public function setButtonType($buttonType): ButtonType
	{
		if (!in_array($buttonType, [self::BUTTON_BUTTON, self::BUTTON_RESET, self::BUTTON_SUBMIT])) {
			return $this;
		}

		$this->buttonType = $buttonType;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getButtonType(): string
	{
	    return $this->buttonType;
	}

}

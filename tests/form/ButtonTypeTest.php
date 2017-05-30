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
 * @version    0.9.2
 * @author     Antares Team
 * @author     Mariusz Jucha <mariuszjucha@gmail.com>
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Form\TestCase;

use Antares\Form\Controls\ButtonType;
use Antares\Testing\ApplicationTestCase;

class ButtonTypeTest extends ApplicationTestCase
{

	/**
	 * Prepare application, autoload etc.
	 */
	public function setUp()
	{
	    parent::setUp();
	}

	/**
	 * @test
	 *
	 * @covers ButtonType::setButtonType()
	 * @covers ButtonType::getButtonType()
	 */
	public function testSetMinValue()
	{
		$control = new ButtonType('button');

		$this->assertEquals(ButtonType::BUTTON_BUTTON, $control->getButtonType());

		$type = ButtonType::BUTTON_SUBMIT;

		$control->setButtonType($type);

		$this->assertEquals($type, $control->getButtonType());

		$control->setButtonType('fake_button_type');

		$this->assertEquals($type, $control->getButtonType());

		$html = (string) $control;

		$this->assertTrue((bool) preg_match(sprintf("/<button(.*?)type=('|\"|)%s('|\"|)/s", $type), $html));
	}

}

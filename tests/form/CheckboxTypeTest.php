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

use Antares\Form\Controls\CheckboxType;
use Antares\Testing\ApplicationTestCase;
use Illuminate\View\View;

class CheckboxTypeTest extends ApplicationTestCase
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
	 * @covers CheckboxType::useHiddenElement()
	 * @covers CheckboxType::setUseHiddenElement()
	 */
	public function testUseHiddenElement()
	{
		$control = new CheckboxType('name');

		$control->setUseHiddenElement(true);

		$this->assertTrue($control->useHiddenElement());

		$control->setUseHiddenElement(false);

		$this->assertFalse($control->useHiddenElement());
	}

	/**
	 * @test
	 *
	 * @covers CheckboxType::setUseHiddenElement()
	 */
	public function testHtmlUseHiddenElement()
	{
		$control = new CheckboxType('name');

		$control->setUseHiddenElement(true);

		$html = (string) $control;

		$this->assertTrue((bool) preg_match("/<input(.*?)type=('|\")hidden('|\")/", $html));
	}
	
	/**
	 * @test
	 *
	 * @covers CheckboxType::setCheckedValue()
	 * @covers CheckboxType::getCheckedValue()
	 * @covers CheckboxType::getUncheckedValue()
	 * @covers CheckboxType::isChecked()
	 * @covers CheckboxType::setValue()
	 */
	public function testCheckboxIsChecked()
	{
		$control = new CheckboxType('name');

		$this->assertFalse($control->isChecked());

		$randomValue = rand(5, 10);

		$control->setCheckedValue($randomValue);
		$control->setValue($randomValue);

		$this->assertTrue($control->isChecked());
		$this->assertEquals($control->getValue(), $control->getCheckedValue());

		do {
			$newRandomValue = rand(5, 10);
		} while($newRandomValue === $randomValue);

		$control->setValue($newRandomValue);

		$this->assertFalse($control->isChecked());
		$this->assertEquals($control->getValue(), $control->getUncheckedValue());
	}

	/**
	 * @test
	 *
	 * @covers CheckboxType::render()
	 */
	public function testRender()
	{
		$control = new CheckboxType('name');

		$view = $control->render();

		$this->assertInstanceOf(View::class, $view);

		$html = (string) $view;

		$this->assertTrue((bool) preg_match("/<input(.*?)type=('|\")checkbox('|\")/s", $html));
	}

}

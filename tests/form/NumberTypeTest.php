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

use Antares\Form\Controls\NumberType;
use Antares\Testing\ApplicationTestCase;
use Illuminate\View\View;

class NumberTypeTest extends ApplicationTestCase
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
	 * @covers NumberType::setMinValue()
	 */
	public function testSetMinValue()
	{
		$control = new NumberType('name');

		$minValue = rand(0, 100);

		$control->setMinValue($minValue);

		$this->assertArrayHasKey('min', $control->getAttributes());
		$this->assertEquals($minValue, $control->getAttribute('min'));
	}

	/**
	 * @test
	 *
	 * @covers NumberType::setMaxValue()
	 */
	public function testSetMaxValue()
	{
		$control = new NumberType('name');

		$maxValue = rand(0, 100);

		$control->setMaxValue($maxValue);

		$this->assertArrayHasKey('max', $control->getAttributes());
		$this->assertEquals($maxValue, $control->getAttribute('max'));
	}

	/**
	 * @test
	 *
	 * @covers NumberType::setMinValue()
	 */
	public function testSetStep()
	{
		$control = new NumberType('name');

		$step = rand(0, 100);

		$control->setStep($step);

		$this->assertArrayHasKey('step', $control->getAttributes());
		$this->assertEquals($step, $control->getAttribute('step'));
	}

}

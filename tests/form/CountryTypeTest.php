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

use Antares\Form\Controls\CountryType;
use Antares\Testing\ApplicationTestCase;
use Illuminate\View\View;

class CountryTypeTest extends ApplicationTestCase
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
	 * @covers CountryType::render()
	 */
	public function testRender()
	{
		$control = $this->getMockBuilder(CountryType::class)
			->setMethods([
				'setCountriesFromDB'
			])
			->disableOriginalConstructor()
			//->setConstructorArgs(['name'])
			->getMock();

		$control->setName('name');
		//$control->expects($this->once())->method('setCountriesFromDB');

		$view = $control->render();

		$this->assertInstanceOf(View::class, $view);

		$html = (string) $view;

		$this->assertTrue((bool) preg_match("/<select(.*?)data-flag-select--search=('|\"|)true('|\"|)/s", $html));
	}

}

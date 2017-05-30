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

use Antares\Form\Controls\DropzoneType;
use Antares\Testing\ApplicationTestCase;

class DropzoneTypeTest extends ApplicationTestCase
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
	 * @covers DropzoneType::render()
	 */
	public function testRender()
	{
		$control = new DropzoneType('name');

//		$html = (string) $control;
//
//		$this->assertArrayHasKey('data-datepicker', $control->getAttributes());
//		$this->assertTrue((bool) preg_match("/<input(.*?)data-datepicker=('|\"|)true('|\"|)/s", $html));
	}

}

<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Form\TestCase;

use Antares\Form\Controls\HiddenType;
use Antares\Form\Decorators\HiddenDecorator;
use Antares\Testing\ApplicationTestCase;
use Illuminate\View\View;

class HiddenTypeTest extends ApplicationTestCase
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
	 * @covers HiddenType::render()
	 */
	public function testRender()
	{
		$control = new HiddenType('name');

		$view = $control->render();

		$this->assertInstanceOf(View::class, $view);
		$this->assertInstanceOf(HiddenDecorator::class, $control->getDecorator());

		$html = (string) $view;

		$this->assertTrue((bool) preg_match("/<input(.*?)type=('|\")hidden('|\")/s", $html));
	}

}

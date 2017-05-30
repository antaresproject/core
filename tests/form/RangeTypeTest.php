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

use Antares\Form\Controls\RangeType;
use Antares\Testing\ApplicationTestCase;
use Illuminate\View\View;

class RangeTypeTest extends ApplicationTestCase
{

	/**
	 * Prepare application, autoload etc.
	 */
	public function setUp()
	{
	    parent::setUp();
	}

	/**
	 * Generate (pseudo)random string
	 *
	 * @return string
	 */
	private function randomString()
	{
		return substr(str_shuffle('abcdefghijklmnoprstwuxyz123456789'), 0, rand(5, 8));
	}

	/**
	 * @test
	 *
	 * @covers RangeType::render()
	 */
	public function testRender()
	{
		$control = new RangeType('name');

		$id = $this->randomString();
		$control->setId($id);

		$view = $control->render();

		$this->assertInstanceOf(View::class, $view);
		$this->assertContains('slider-val', $control->getAttribute('class'));

		$html = (string) $view;

		$this->assertTrue((bool) preg_match(
			sprintf("/<div id=('|\")slider-%s('|\") data-slider=('|\")true('|\")><\/div>/s", $id), $html
		));
	}

}

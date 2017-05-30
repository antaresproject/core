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

use Antares\Form\Controls\Elements\OptGroup;
use Antares\Form\Controls\SelectType;
use Antares\Testing\ApplicationTestCase;
use Illuminate\View\View;

class SelectTypeTest extends ApplicationTestCase
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
	 * @return array
	 */
	private function selectValueOptions()
	{
		return [
			1 => 'One',
			2 => 'Two',
			5 => 'Five',
			'Ten' => [
				100 => 'One hundred',
				200 => 'Two hundreds',
				300 => 'Three hundreds'
			]
		];
	}

	/**
	 * @test
	 *
	 * @covers SelectType::setValueOptions()
	 * @covers SelectType::getValueOptions()
	 */
	public function testSetValueOptions()
	{
	    $element = new SelectType('name');

		$element->setValueOptions($this->selectValueOptions());
		$options = $element->getValueOptions();

		$this->assertTrue(is_array($options));
		$this->assertArrayHasKey(5, $options);
		$this->assertArrayHasKey('Ten', $options);
		$this->assertInstanceOf(OptGroup::class, $options['Ten']);
		$this->assertEquals(count($options), count($this->selectValueOptions()));
	}

	/**
	 * @test
	 *
	 * @covers SelectType::hasEmptyValue()
	 * @covers SelectType::setEmptyValue()
	 * @covers SelectType::getEmptyValue()
	 */
	public function testEmptyOption()
	{
		$element = new SelectType('name');

		$this->assertFalse($element->hasEmptyValue());

		$emptyValue = $this->randomString();

		$element->setEmptyValue($emptyValue);

		$this->assertTrue($element->hasEmptyValue());
		$this->assertEquals($emptyValue, $element->getEmptyValue());
	}

	/**
	 * @test
	 *
	 * @covers SelectType::setMultiple()
	 * @covers SelectType::isMultiple()
	 */
	public function testMultiple()
	{
		$element = new SelectType('name');

		$this->assertArrayNotHasKey('multiple', $element->getAttributes());

		$element->setMultiple(true);

		$this->assertTrue($element->isMultiple());
		$this->assertArrayHasKey('multiple', $element->getAttributes());
		$this->assertEquals($element->getAttribute('multiple'), 'multiple');

		$element->setMultiple(false);

		$this->assertFalse($element->isMultiple());
		$this->assertArrayNotHasKey('multiple', $element->getAttributes());
	}

	/**
	 * @test
	 *
	 * @covers SelectType::render()
	 */
	public function testRender()
	{
		$control = new SelectType('name');

		$view = $control->render();

		$this->assertInstanceOf(View::class, $view);

		$html = (string) $view;

		$this->assertTrue((bool) preg_match("/<select(.*?)name=('|\")name('|\")/s", $html));
	}

}

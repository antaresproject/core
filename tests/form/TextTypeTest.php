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

use Antares\Form\Controls\AbstractType;
use Antares\Form\Controls\TextType;
use Antares\Form\Decorators\HorizontalDecorator;
use Antares\Form\Labels\AbstractLabel;
use Antares\Form\Labels\Label;
use Antares\Testing\ApplicationTestCase;
use Illuminate\View\View;

class TextTypeTest extends ApplicationTestCase
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
	 * @covers AbstractType::getType()
	 */
    public function testControlType()
    {
	    $this->assertEquals('text', (new TextType('name'))->getType());
    }

	/**
	 * @test
	 *
	 * @covers AbstractType::getAttribute()
	 * @covers AbstractType::getAttributes()
	 */
    public function testControlHasAttributesPassedInConstructor()
    {
        $control = new TextType('name', [
        	'attr1' => 'attr1_value',
	        'attr2' => 'attr2_value'
        ]);

        $this->assertEquals('attr1_value', $control->getAttribute('attr1'));
        $this->assertEquals('attr2_value', $control->getAttribute('attr2'));
        $this->assertArrayNotHasKey('attr3', $control->getAttributes());
    }

	/**
	 * @test
	 *
	 * @covers AbstractType::getAttribute()
	 * @covers AbstractType::setAttribute()
	 */
    public function testControlHasAttribute()
    {
        $control = new TextType('name');

        $control->setAttribute('attr', 'value');

        $this->assertEquals('value', $control->getAttribute('attr'));
    }

	/**
	 * @test
	 *
	 * @covers AbstractType::getAttribute()
	 * @covers AbstractType::setAttributes()
	 */
    public function testControlHasAttributes()
    {
        $control = new TextType('name');

	    $control->setAttributes([
	    	'attr1' => 'value1',
		    'attr2' => 'value2'
	    ]);

	    $this->assertEquals('value1', $control->getAttribute('attr1'));
	    $this->assertEquals('value2', $control->getAttribute('attr2'));
    }

	/**
	 * @test
	 *
	 * @covers AbstractType::getAttributes()
	 * @covers AbstractType::addClass()
	 */
    public function testAddingClassAttribute()
    {
		$control = new TextType('name');

		$control->setAttributes(['class' => 'some-class']);
		$control->addClass('another-class');

		$this->assertArrayHasKey('class', $control->getAttributes());
		$this->assertTrue(strpos($control->getAttributes()['class'], 'another-class') !== false);
		$this->assertTrue(count(explode(' ', $control->getAttributes()['class'])) === 2);
    }

	/**
	 * @test
	 *
	 * @covers AbstractType::getId()
	 */
    public function testGetControlId()
    {
		$control = new TextType('name');

		$this->assertTrue(empty($control->getId()));
    }

	/**
	 * @test
	 *
	 * @covers AbstractType::setId()
	 * @covers AbstractType::getId()
	 */
    public function testSetControlId()
    {
        $control = new TextType('name');

        $id = $this->randomString();

        $control->setId($id);

        $this->assertEquals($id, $control->getId());
    }

	/**
	 * @test
	 *
	 * @covers AbstractType::setLabel()
	 * @covers AbstractType::getLabel()
	 * @covers AbstractLabel::getName()
	 * @covers AbstractType::hasLabel()
	 */
    public function testSetControlLabelAsString()
    {
		$control = new TextType('name');

		$label = $this->randomString();

		$this->assertFalse($control->hasLabel());

		$control->setLabel($label);

		$this->assertInstanceOf(AbstractLabel::class, $control->getLabel());
		$this->assertEquals($control->getLabel()->getName(), $label);
		$this->assertTrue($control->hasLabel());
    }

	/**
	 * @test
	 *
	 * @covers AbstractType::setLabel()
	 * @covers AbstractType::getLabel()
	 * @covers AbstractType::hasLabel()
	 */
    public function testSetControlLabelAsObject()
    {
	    $labelName = $this->randomString();

        $control = new TextType('name');
        $label = new Label($labelName);

        $this->assertFalse($control->hasLabel());

        $control->setLabel($label);

		$labelObjectHash = spl_object_hash($label);

		$this->assertEquals(spl_object_hash($control->getLabel()), $labelObjectHash);
		$this->assertEquals($control->getLabel()->getName(), $labelName);
		$this->assertTrue($control->hasLabel());
    }

	/**
	 * @test
	 *
	 * @covers AbstractType::getPrependHtml()
	 * @covers AbstractType::setPrependHtml()
	 */
    public function testPrependHtml()
    {
        $control = new TextType('name');

        $html = $this->randomString();

        $control->setPrependHtml($html);

        $this->assertEquals($html, $control->getPrependHtml());
    }

	/**
	 * @test
	 *
	 * @covers AbstractType::getAppendHtml()
	 * @covers AbstractType::setAppendHtml()
	 */
	public function testAppendHtml()
	{
		$control = new TextType('name');

		$html = $this->randomString();

		$control->setAppendHtml($html);

		$this->assertEquals($html, $control->getAppendHtml());
	}

	/**
	 * @test
	 *
	 * @covers AbstractType::setDecorator()
	 * @covers AbstractType::getDecorator()
	 */
	public function testSetDecoratorAsString()
	{
	    $control = new TextType('name');

		$decorator = HorizontalDecorator::class;

		$control->setDecorator($decorator);

		$this->assertInstanceOf($decorator, $control->getDecorator());
	}

	/**
	 * @test
	 *
	 * @covers AbstractType::setDecorator()
	 * @covers AbstractType::getDecorator()
	 */
	public function testSetDecoratorAsObject()
	{
		$control = new TextType('name');

		$decorator = new HorizontalDecorator();
		$decoratorObjectHash = spl_object_hash($decorator);

		$control->setDecorator($decorator);

		$this->assertEquals(spl_object_hash($decorator), $decoratorObjectHash);
	}

	/**
	 * @test
	 *
	 * @covers AbstractType::getName()
	 * @covers AbstractType::setName()
	 */
	public function testControlName()
	{
		$controlName = $this->randomString();
		$newControlName = $this->randomString();

	    $control = new TextType($controlName);

	    $this->assertEquals($controlName, $control->getName());

	    $control->setName($newControlName);

	    $this->assertEquals($newControlName, $control->getName());
	}

	/**
	 * @test
	 *
	 * @covers AbstractType::setType()
	 */
	public function testSetControlType()
	{
	    $control = new TextType('name');

	    $type = 'radio';
	    $control->setType($type);

	    $this->assertEquals($control->getType(), $type);
	}

	/**
	 * @test
	 *
	 * @covers AbstractType::getValue()
	 * @covers AbstractType::setValue()
	 */
	public function testControlValue()
	{
		$control = new TextType('name');

		$value = $this->randomString();

		$control->setValue($value);

		$this->assertEquals($value, $control->getValue());
	}

	/**
	 * @test
	 *
	 * @covers AbstractType::setPlaceholder()
	 */
	public function testSetPlaceholder()
	{
	    $control = new TextType('name');

	    $placeholder = $this->randomString();

	    $control->setPlaceholder($placeholder);

	    $this->assertArrayHasKey('placeholder', $control->getAttributes());
	    $this->assertEquals($placeholder, $control->getAttribute('placeholder'));
	}

	/**
	 * @test
	 *
	 * @covers AbstractType::addMessage()
	 * @covers AbstractType::getMessages()
	 */
	public function testAddMessage()
	{
		$control = new TextType('name');

		$message = $this->randomString();
		$type = 'infos';

		$control->addMessage($type, $message);

		$this->assertEquals(1, count($control->getMessages()));
		$this->assertArrayHasKey($type, $control->getMessages());
		$this->assertTrue(in_array($message, $control->getMessages()[$type]));
	}

	/**
	 * @test
	 *
	 * @covers AbstractType::addError()
	 */
	public function testAddErrorMessage()
	{
	    $control = new TextType('name');

	    $message = $this->randomString();

	    $control->addError($message);

	    $this->assertEquals(1, count($control->getMessages()));
	    $this->assertArrayHasKey('errors', $control->getMessages());
	    $this->assertTrue(in_array($message, $control->getMessages()['errors']));
	}

	/**
	 * @test
	 * 
	 * @covers AbstractType::render()
	 */
	public function testRenderControl()
	{
	    $control = new TextType('name');

	    $view = $control->renderControl();
	    $html = $view->render();

	    $this->assertInstanceOf(View::class, $view);
	    $this->assertContains($control->getType(), $html);
	    $this->assertContains($control->getName(), $html);
	}

	/**
	 * @test
	 *
	 * @covers AbstractType::__toString()
	 */
	public function testTreatControlObjectLikeString()
	{
	    $control = new TextType('name');

	    $html = (string) $control;

		$this->assertTrue(is_string($html));
	}

}

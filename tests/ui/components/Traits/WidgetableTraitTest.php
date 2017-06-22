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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Widgets\Traits\Tests;

use Antares\UI\UIComponents\Traits\ComponentTrait as Stub;
use Antares\UI\UIComponents\Model\Components;
use Illuminate\Database\Eloquent\Model;
use Antares\Testing\TestCase;
use Illuminate\View\View;

class WidgetableTraitTest extends TestCase
{

    use Stub;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var String
     */
    protected $description;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $childs;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * @var View
     */
    protected $view;

    /**
     * @see parent::getName()
     * 
     * @param boolean $withDataSet
     * @return String
     */
    public function getName($withDataSet = true)
    {
        return parent::getName($withDataSet);
    }

    /**
     * @see parent::get($uri,$headers)
     * 
     * @param String $uri
     * @param array $headers
     * @return String | mixed
     */
    public function get($uri, array $headers = array())
    {
        return parent::get($uri, $headers);
    }

    /**
     * test \Antares\UI\UIComponents\Traits\WidgetableTrait::getRules()
     * 
     * @test
     */
    public function testGetRules()
    {
        $this->rules = ['foo'];
        $this->assertEquals($this->getRules(), $this->rules);
    }

    /**
     * test \Antares\UI\UIComponents\Traits\WidgetableTrait::getDescription()
     * 
     * @test
     */
    public function testGetDescription()
    {
        $this->description = 'foo';
        $this->assertSame($this->getDescription(), $this->description);
    }

    /**
     * test \Antares\UI\UIComponents\Traits\WidgetableTrait::getModel()
     * 
     * @test
     */
    public function testGetModel()
    {
        $this->model = new Components();
        $this->assertInstanceOf('\ArrayAccess', $this->getModel());
    }

    /**
     * test \Antares\UI\UIComponents\Traits\WidgetableTrait::getAttributes()
     * 
     * @test
     */
    public function testGetAttributes()
    {
        $this->attributes = ['foo'];
        $this->assertEquals($this->getAttributes(), $this->attributes);
    }

    /**
     * test \Antares\UI\UIComponents\Traits\WidgetableTrait::getParam()
     * 
     * @test
     */
    public function testGetParam()
    {
        $this->params = ['name' => 'foo'];
        $this->assertSame('foo', $this->getParam('name'));
        $this->assertSame('bar', $this->getParam('foo', 'bar'));
    }

    /**
     * test \Antares\UI\UIComponents\Traits\WidgetableTrait::getDefaults()
     * 
     * @test
     */
    public function testGetDefaults()
    {
        $this->defaults = [
            'x'      => 0,
            'y'      => 0,
            'width'  => '4',
            'height' => '4'
        ];
        $this->assertEquals($this->defaults, $this->getDefaults());
        $this->assertEquals(['x' => 0, 'y' => 0], $this->getDefaults(['x', 'y']));
    }

    /**
     * test \Antares\UI\UIComponents\Traits\WidgetableTrait::setView()
     * 
     * @test
     */
    public function testSetView()
    {
        $this->assertInstanceOf(get_class($this), $this->setView('foo'));
        $this->assertSame('foo', $this->view);
    }

}

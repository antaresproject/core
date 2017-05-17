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

namespace Antares\Widgets\Adapter\Tests;

use Antares\Widgets\Adapter\AttributesAdapter as Stub;
use Antares\Widgets\Adapter\AttributesAdapter;
use Antares\Widgets\WidgetsServiceProvider;
use Antares\Testing\TestCase;

class AttributesAdapterTest extends TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $serviceProvider = new WidgetsServiceProvider($this->app);
        $serviceProvider->register();
        $serviceProvider->bootExtensionComponents();
    }

    /**
     * test Antares\Widgets\Adapter\AttributesAdapter::__construct
     * 
     * @test
     */
    public function testConstruct()
    {
        $instance = new Stub('foo');
        $this->assertInstanceOf(AttributesAdapter::class, $instance);
    }

    /**
     * test \Antares\Widgets\Adapter\AttributesAdapter::defaults()
     * 
     * @test
     */
    public function testDefaults()
    {
        $instance = new Stub('foo');
        $defaults = $instance->defaults();
        $this->assertTrue(!empty($defaults));
        $this->assertTrue(array_has($defaults, 'min_width'));
    }

    /**
     * test \Antares\Widgets\Adapter\AttributesAdapter::options()
     * 
     * @test
     */
    public function testOptions()
    {
        $instance = new Stub('foo');
        $options  = $instance->options();
        $this->assertTrue(!empty($options));
        $this->assertTrue(array_has($options, 'id'));
        $this->assertTrue(array_has($options, 'data'));
        $this->assertTrue(array_has($options, 'widgets'));

        $instance2 = new Stub('foo', ['id' => 'foo']);
        $options2  = $instance2->options();
        $this->assertSame('foo', $options2['id']);
    }

    /**
     * test \Antares\Widgets\Adapter\AttributesAdapter::attributes()
     * 
     * @test
     */
    public function testAttributes()
    {
        $instance   = new Stub('foo');
        $attributes = $instance->attributes();
        $this->assertTrue(!empty($attributes));
        $this->assertTrue(array_has($attributes, 'min_width'));

        $attributes2 = $instance->attributes(['min_width' => 'foo']);
        $this->assertSame('foo', $attributes2['min_width']);
    }

}

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
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Factories\TestCase;

use Antares\Foundation\Support\Factories\MenuFactory;
use Mockery as m;
use Antares\Testing\TestCase;

class MenuFactoryTest extends TestCase
{

    /**
     * test constructing
     * @test
     */
    public function testConstructing()
    {
        $stub = new MenuFactory(m::mock('Illuminate\Contracts\Container\Container'));
        $this->assertInstanceOf('\Antares\Foundation\Support\Factories\MenuFactory', $stub);
    }

    /**
     * @test
     * test with method
     */
    public function testWith()
    {
        $stub  = new MenuFactory($this->app);
        $stub->with('menu.top.foo');
        $menu  = $this->app['antares.widget']->of('menu.top.foo');
        $this->assertInstanceOf(\Antares\UI\TemplateBase\Menu::class, $menu);
        $menu->add('foo', 'foo');
        $items = $menu->items();
        $this->assertInstanceOf('Antares\Support\Collection', $menu->items());
        $this->assertTrue($items->has('foo'));
        $this->assertEquals('foo', $items->get('foo')->id);
    }

    /**
     * test withHanlders method
     */
    public function testWithHandlers()
    {
        $stub = new MenuFactory($this->app);
        try {
            $stub->withHandlers('String');
        } catch (\Exception $e) {
            $this->assertInstanceOf('ReflectionException', $e);
        }
        $menuHandler = m::mock('Antares\Acl\Http\Handlers\RoleMenuHandler');
        $this->assertInstanceOf('\Antares\Foundation\Support\Factories\MenuFactory', $stub->withHandlers($menuHandler));
    }

    /**
     * test compose method
     */
    public function testCompose()
    {
        $stub        = new MenuFactory($this->app);
        $menuHandler = m::mock('Antares\Acl\Http\Handlers\RoleMenuHandler');
        $stub->with('menu.top.foo');
        $stub->withHandlers($menuHandler);
        $this->assertInstanceOf('\Antares\Foundation\Support\Factories\MenuFactory', $stub->compose('*'));
    }

}

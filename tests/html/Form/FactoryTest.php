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

namespace Antares\Html\Form\TestCase;

use Antares\Testing\ApplicationTestCase;
use Illuminate\Container\Container;
use Antares\Html\Form\Factory;
use Mockery as m;

class FactoryTest extends ApplicationTestCase
{

    /**
     * Test Antares\Html\Table\Factory::make() method.
     *
     * @test
     */
    public function testMakeMethod()
    {
        $stub   = new Factory($this->getContainer());
        $output = $stub->make(function () {
            
        });

        $this->assertInstanceOf('\Antares\Html\Form\FormBuilder', $output);
    }

    /**
     * Test Antares\Html\Form\Factory::of() method.
     *
     * @test
     */
    public function testOfMethod()
    {
        $stub   = new Factory($this->getContainer());
        $output = $stub->of('foo', function () {
            
        });

        $this->assertInstanceOf('\Antares\Html\Form\FormBuilder', $output);
    }

    /**
     * Test Antares\Html\Form\Factory pass-through method
     * to \Illuminate\Html\FormBuilder.
     *
     * @test
     */
    public function testPassThroughMethod()
    {
        $app         = new Container();
        $app['form'] = $form        = m::mock('\Illuminate\Html\FormBuilder');

        $form->shouldReceive('hidden')->once()->with('foo', 'bar')->andReturn('foobar');

        $stub   = new Factory($app);
        $output = $stub->hidden('foo', 'bar');

        $this->assertEquals('foobar', $output);
    }

    /**
     * Get app container.
     *
     * @return Container
     */
    protected function getContainer()
    {
        $app    = new Container();
        $config = m::mock('\Illuminate\Contracts\Config\Repository');

        $app['request']                                = m::mock('\Illuminate\Http\Request');
        $app['translator']                             = m::mock('\Illuminate\Translation\Translator');
        $app['view']                                   = m::mock('\Illuminate\Contracts\View\Factory');
        $app['Illuminate\Contracts\Config\Repository'] = $config;

        $config->shouldReceive('get')->once()
                ->with('antares/html::form', [])->andReturn([]);

        return $app;
    }

}

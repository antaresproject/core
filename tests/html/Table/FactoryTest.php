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
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
 namespace Antares\Html\Table\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Html\Table\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

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

        $this->assertInstanceOf('\Antares\Html\Table\TableBuilder', $output);
    }

    /**
     * Test Antares\Html\Table\Factory::of() method.
     *
     * @test
     */
    public function testOfMethod()
    {
        $stub   = new Factory($this->getContainer());
        $output = $stub->of('foo', function () {
                    });

        $this->assertInstanceOf('\Antares\Html\Table\TableBuilder', $output);
    }

    /**
     * Get app container.
     *
     * @return Container
     */
    protected function getContainer()
    {
        $app  = new Container();
        $app['Illuminate\Contracts\Config\Repository'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['request'] = m::mock('\Illuminate\Http\Request');
        $app['translator'] = m::mock('\Illuminate\Translation\Translator');
        $app['view'] = m::mock('\Illuminate\Contracts\View\Factory');

        $config->shouldReceive('get')->once()
            ->with('antares/html::table', [])->andReturn([]);

        return $app;
    }
}

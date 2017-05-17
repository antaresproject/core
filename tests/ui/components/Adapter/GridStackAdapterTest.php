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

use Antares\Widgets\Adapter\GridStackAdapter as Stub;
use Illuminate\Contracts\Config\Repository;
use Antares\Widgets\Contracts\GridStack;
use Antares\Testing\TestCase;
use Antares\Asset\Factory;
use Mockery as m;

class GridStackAdapterTest extends TestCase
{

    /**
     * \Antares\Widgets\Adapter\GridStackAdapter::__construct
     * 
     * @test 
     */
    public function testConstruct()
    {
        $asset      = m::mock(Factory::class);
        $repository = m::mock(Repository::class);
        $stub       = new Stub($repository, $asset);
        $this->assertInstanceOf(GridStack::class, $stub);
    }

    /**
     * \Antares\Widgets\Adapter\GridStackAdapter::scripts()
     * 
     * @test 
     */
    public function testScripts()
    {

        $asset = m::mock(Factory::class);
        $asset->shouldReceive('container')
                ->with('foo')
                ->andReturnSelf()
                ->shouldReceive('add')
                ->with('foo', __DIR__)
                ->andReturn(true)
                ->shouldReceive('inlineScript')
                ->with('grid-stack', m::type('String'))
                ->andReturn(true);

        $repository = m::mock(Repository::class);
        $repository->shouldReceive('get')->with('antares/widgets::gridstack.placeholder')->andReturn('foo')
                ->shouldReceive('get')->with('antares/widgets::gridstack.resources')->andReturn(['foo' => __DIR__]);


        $stub = new Stub($repository, $asset);
        $this->assertNull($stub->scripts());
    }

}

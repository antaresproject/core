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

namespace Antares\Memory\Handlers\TestCase;

use Antares\Memory\Handlers\Registry;
use Illuminate\Support\Fluent;
use Mockery as m;

class ResgistryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Add data provider.
     *
     * @return array
     */
    protected function eloquentDataProvider()
    {
        return [
            new Fluent(['id' => 1, 'name' => 'foo', 'value' => 'foobar']),
            new Fluent(['id' => 2, 'name' => 'hello', 'value' => 'world']),
        ];
    }

    /**
     * Test Antares\Memory\RegistryMemoryHandler::initiate() method.
     *
     * @test
     */
    public function testInitiateMethod()
    {
        $app      = m::mock('\Illuminate\Container\Container');
        $cache    = m::mock('\Illuminate\Contracts\Cache\Repository');
        $eloquent = m::mock('EloquentHandlerModelMock');

        $config = ['model' => 'EloquentHandlerModelMock', 'cache' => true];
        $data   = $this->eloquentDataProvider();

        $app->shouldReceive('make')->with('EloquentHandlerModelMock')->andReturn($eloquent);

        $eloquent->shouldReceive('newInstance')->andReturn($eloquent)
                ->shouldReceive('get')->andReturn($data);

        $stub     = new Registry('stub', $config, $app, $cache);
        $expected = [
            'foo'   => 'foobar',
            'hello' => 'world',
        ];


        $this->assertInstanceOf('\Antares\Memory\Handlers\Registry', $stub);
        $this->assertEquals($expected, $stub->initiate());
    }

    /**
     * Test Antares\Memory\RegistryMemoryHandler::finish() method.
     *
     * @test
     */
    public function testFinishMethod()
    {
        $app      = m::mock('\Illuminate\Container\Container');
        $cache    = m::mock('\Illuminate\Contracts\Cache\Repository');
        $eloquent = m::mock('EloquentHandlerModelMock');


        $data = $this->eloquentDataProvider();



        $app->shouldReceive('make')->times(4)->with('EloquentHandlerModelMock')->andReturn($eloquent);
        $cache->shouldReceive('forget')->once()->with('db-memory:eloquent-stub')->andReturn(null);


        $eloquent->shouldReceive('newInstance')->times(4)->andReturn($eloquent)
                ->shouldReceive('get')->once()->andReturn($data)
                ->shouldReceive('create')->once()->andReturn(true);

        $checkWithWhereFooQuery = m::mock('\Illuminate\Database\Query\Builder');
        $checkWithWhereFooQuery->shouldReceive('first')->once()->andReturnNull();

        $eloquent->shouldReceive('where')->once()->with('name', '=', 'foo')->andReturn($checkWithWhereFooQuery);



        $checkWithWhereStubbedQuery = m::mock('\Illuminate\Database\Query\Builder');
        $checkWithWhereStubbedQuery->shouldReceive('first')->once()->andReturnNull();
        $eloquent->shouldReceive('where')->with('name', '=', 'stubbed')->andReturn($checkWithWhereStubbedQuery);



        $config = ['model' => $eloquent, 'cache' => true];

        $stub = new Registry('stub', $config, $app, $cache);
        $stub->initiate();

        $items = [
            'foo'     => 'foobar is wicked',
            'hello'   => 'world',
            'stubbed' => 'Super Stubbed',
        ];

        $this->assertTrue($stub->finish($items));
    }

}

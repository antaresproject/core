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

use Mockery as m;
use Illuminate\Support\Fluent;
use Antares\Memory\Handlers\Eloquent;

class EloquentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Add data provider.
     *
     * @return array
     */
    protected function eloquentDataProvider()
    {
        return [
            new Fluent(['id' => 1, 'name' => 'foo', 'value' => 's:6:"foobar";']),
            new Fluent(['id' => 2, 'name' => 'hello', 'value' => 's:5:"world";']),
        ];
    }

    /**
     * Test Antares\Memory\EloquentMemoryHandler::initiate() method.
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
        $cache->shouldReceive('rememberForever')
                ->with('db-memory:eloquent-stub', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) {
                    return $c();
                });
        $eloquent->shouldReceive('newInstance')->andReturn($eloquent)
                ->shouldReceive('get')->andReturn($data);

        $stub = new Eloquent('stub', $config, $app, $cache);

        $expected = [
            'foo'   => 'foobar',
            'hello' => 'world',
        ];

        $this->assertInstanceOf('\Antares\Memory\Handlers\Eloquent', $stub);
    }

    /**
     * Test Antares\Memory\EloquentMemoryHandler::finish() method.
     *
     * @test
     */
    public function testFinishMethod()
    {
        $app      = m::mock('\Illuminate\Container\Container');
        $cache    = m::mock('\Illuminate\Contracts\Cache\Repository');
        $eloquent = m::mock('EloquentHandlerModelMock');

        $config = ['model' => $eloquent, 'cache' => true];
        $data   = $this->eloquentDataProvider();

        $checkWithCountQuery    = m::mock('\Illuminate\Database\Query\Builder');
        $checkWithoutCountQuery = m::mock('\Illuminate\Database\Query\Builder');
        $fooEntity              = m::mock('FooEntityMock');

        $app->shouldReceive('make')->with('EloquentHandlerModelMock')->andReturn($eloquent);
        $cache->shouldReceive('rememberForever')
                ->with('db-memory:eloquent-stub', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) {
                    return $c();
                })
                ->shouldReceive('forget')->with('db-memory:eloquent-stub')->andReturn(null);
        $eloquent->shouldReceive('newInstance')->andReturn($eloquent)
                ->shouldReceive('get')->andReturn($data)
                ->shouldReceive('create')->andReturn(true)
                ->shouldReceive('where')->with('name', '=', 'foo')->andReturn($checkWithCountQuery)
                ->shouldReceive('where')->with('name', '=', 'hello')->andReturn($checkWithCountQuery)
                ->shouldReceive('where')->with('name', '=', 'stubbed')->andReturn($checkWithoutCountQuery);
        $checkWithCountQuery->shouldReceive('first')->andReturn($fooEntity);
        $checkWithoutCountQuery->shouldReceive('first')->andReturnNull();
        $fooEntity->shouldReceive('save')->andReturn(true);

        $stub = new Eloquent('stub', $config, $app, $cache);
    }

}

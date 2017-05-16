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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Memory\Tests;

use Antares\Tester\Memory\Handler as Stub;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Query\Builder;
use Illuminate\Container\Container;
use Antares\Tester\Memory\Handler;
use Illuminate\Support\Fluent;
use Antares\Testing\TestCase;
use Mockery as m;

class HandlerTest extends TestCase
{

    /**
     * Add data provider.
     *
     * @return array
     */
    protected function eloquentDataProvider()
    {
        return [
            new Fluent(['id' => 1, 'name' => 'foo', 'value' => 'a:1:{s:4:"test";s:6:"foobar";}']),
            new Fluent(['id' => 2, 'name' => 'hello', 'value' => 'a:1:{s:4:"test";s:5:"world";}']),
        ];
    }

    /**
     * Test Antares\Tester\Memory\Hander::initiate() method.
     *
     * @test
     */
    public function testInitiateMethod()
    {
        $app      = m::mock(Container::class);
        $cache    = m::mock(Repository::class);
        $eloquent = m::mock('EloquentHandlerModelMock');

        $config = ['model' => 'EloquentHandlerModelMock', 'cache' => true];
        $data   = $this->eloquentDataProvider();

        $app->shouldReceive('make')->with('EloquentHandlerModelMock')->andReturn($eloquent);

        $eloquent->shouldReceive('newInstance')
                ->andReturn($eloquent)
                ->shouldReceive('get')
                ->andReturn($data);

        $cache->shouldReceive('rememberForever')
                ->with('db-memory:eloquent-stub', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) {
                    return $c();
                });

        $stub = new Stub('stub', $config, $app, $cache);

        $expected = [
            'foo'   => [
                'test' => 'foobar',
                'id'   => 1
            ],
            'hello' => [
                'test' => 'world',
                'id'   => 2
            ],
        ];
        $this->assertInstanceOf(Handler::class, $stub);
        $this->assertEquals($expected, $stub->initiate());
    }

    /**
     * Test Antares\Tester\Memory\Hander::finish() method.
     *
     * @test
     */
    public function testFinishMethod()
    {
        $app      = m::mock(Container::class);
        $cache    = m::mock(Repository::class);
        $eloquent = m::mock('EloquentHandlerModelMock');

        $config = ['model' => $eloquent, 'cache' => true];
        $data   = $this->eloquentDataProvider();

        $checkWithCountQuery    = m::mock(Builder::class);
        $checkWithoutCountQuery = m::mock(Builder::class);
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

        $stub  = new Stub('stub', $config, $app, $cache);
        $stub->initiate();
        $items = [
            'foo'     => 'foobar is wicked',
            'hello'   => 'world',
            'stubbed' => 'Foobar was awesome',
        ];
        $this->assertTrue($stub->finish($items));
    }

}

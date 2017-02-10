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


/**
 * BillEvo (http://billevo.com/)
 *
 * @link      http://billevo.com/billevo/docs for documenation
 * @copyright Copyright (c) 2015 BillEvo S.A. (http://billevo.com/)
 * @license   http://billevo.com/license BillEvo License 
 * @package Antares/Tests
 */

namespace Antares\Memory\Handlers\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Antares\Memory\Handlers\Registry;

class ResgistryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

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
        $cache->shouldReceive('rememberForever')
                ->with('db-memory:eloquent-stub', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) {
                    return $c();
                });
        $eloquent->shouldReceive('newInstance')->andReturn($eloquent)
                ->shouldReceive('get')->andReturn($data);

        $stub     = new Registry('stub', $config, $app, $cache);
        $expected = [
            'foo-foobar'  => 'foobar',
            'hello-world' => 'world',
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

        $checkWithCountQuery    = m::mock('\Illuminate\Database\Query\Builder');
        $checkWithoutCountQuery = m::mock('\Illuminate\Database\Query\Builder');
        $fooEntity              = m::mock('FooEntityMock');

        $app->shouldReceive('make')->times(4)->with('EloquentHandlerModelMock')->andReturn($eloquent);
        $cache->shouldReceive('rememberForever')->once()
                ->with('db-memory:eloquent-stub', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) {
                    return $c();
                })
                ->shouldReceive('forget')->once()->with('db-memory:eloquent-stub')->andReturn(null);

        $checkWithCountQuery->shouldReceive('first')->andReturn($fooEntity);

        $checkWithoutCountQuery->shouldReceive('first')->andReturnNull();

        $fooEntity->shouldReceive('save')->times(3)->andReturn(true);

        $eloquent->shouldReceive('newInstance')->times(4)->andReturn($eloquent)
                ->shouldReceive('get')->once()->andReturn($data)
                ->shouldReceive('create')->andReturn(true);

        $checkWithWhereFooQuery = m::mock('\Illuminate\Database\Query\Builder');
        $checkWithWhereFooQuery->shouldReceive('where')->with('value', '=', 'foobar is wicked')->andReturn($checkWithCountQuery);

        $eloquent->shouldReceive('where')->with('name', '=', 'foo')->andReturn($checkWithWhereFooQuery);


        $checkWithWhereWorldQuery = m::mock('\Illuminate\Database\Query\Builder');
        $checkWithWhereWorldQuery->shouldReceive('where')->with('value', '=', 'world')->andReturn($checkWithCountQuery);
        $eloquent->shouldReceive('where')->with('name', '=', 'hello')->andReturn($checkWithWhereWorldQuery);

        $checkWithWhereStubbedQuery = m::mock('\Illuminate\Database\Query\Builder');
        $checkWithWhereStubbedQuery->shouldReceive('where')->with('value', '=', 'Super Stubbed')->andReturn($checkWithCountQuery);
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

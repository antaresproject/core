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

use Antares\Memory\Handlers\Fluent;
use Mockery as m;

class FluentTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Add data provider.
     *
     * @return array
     */
    protected function fluentDataProvider()
    {
        return [
            new \Illuminate\Support\Fluent(['id' => 1, 'name' => 'foo', 'value' => 's:6:"foobar";']),
            new \Illuminate\Support\Fluent(['id' => 2, 'name' => 'hello', 'value' => 's:5:"world";']),
        ];
    }

    /**
     * Test Antares\Memory\Handlers\Fluent::initiate() method.
     *
     * @test
     */
    public function testInitiateMethod()
    {
        $cache = m::mock('\Illuminate\Contracts\Cache\Repository');
        $db    = m::mock('\Illuminate\Database\DatabaseManager');

        $config = ['table' => 'antares_options', 'cache' => true];
        $data   = $this->fluentDataProvider();

        $query = m::mock('\Illuminate\Database\Query\Builder');

        $query->shouldReceive('get')->andReturn($data);

        $stub = new Fluent('stub', $config, $db, $cache);

        $expected = [
            'foo'   => 'foobar',
            'hello' => 'world',
        ];

        $this->assertInstanceOf('\Antares\Memory\Handlers\Fluent', $stub);
    }

    /**
     * Test Antares\Memory\Handlers\Fluent::finish() method.
     *
     * @test
     * @group support
     */
    public function testFinishMethod()
    {
        $cache = m::mock('\Illuminate\Contracts\Cache\Repository');
        $db    = m::mock('\Illuminate\Database\DatabaseManager');

        $config = ['table' => 'antares_options', 'cache' => true];
        $data   = $this->fluentDataProvider();

        $selectQuery            = m::mock('\Illuminate\Database\Query\Builder');
        $checkWithCountQuery    = m::mock('\Illuminate\Database\Query\Builder');
        $checkWithoutCountQuery = m::mock('\Illuminate\Database\Query\Builder');

        $checkWithCountQuery->shouldReceive('count')->andReturn(1);
        $checkWithoutCountQuery->shouldReceive('count')->andReturn(0);
        $selectQuery
                ->shouldReceive('where')->with('name', '=', 'foo')->andReturn($checkWithCountQuery)
                ->shouldReceive('where')->with('name', '=', 'hello')->andReturn($checkWithCountQuery)
                ->shouldReceive('where')->with('name', '=', 'stubbed')->andReturn($checkWithoutCountQuery)
                ->shouldReceive('get')->andReturn($data)
                ->shouldReceive('where')->with('id', '=', 1)->andReturn($selectQuery);

        $stub = new Fluent('stub', $config, $db, $cache);
    }

}

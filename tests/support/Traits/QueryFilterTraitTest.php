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

namespace Antares\Support\Traits\TestCase;

use Mockery as m;
use Antares\Support\Traits\QueryFilterTrait;

class QueryFilterTraitTest extends \PHPUnit_Framework_TestCase
{

    use QueryFilterTrait;

    /**
     * Test \Antares\Support\Traits\QueryFilterTrait::setupBasicQueryFilter()
     * method.
     *
     * @test
     */
    public function testSetupBasicQueryFilterMethod()
    {
        $query = m::mock('\Illuminate\Database\Query\Builder');

        $query->shouldReceive('orderBy')->once()->with('updated_at', 'DESC')->andReturn($query)
                ->shouldReceive('orderBy')->once()->with('created_at', 'DESC')->andReturn($query);

        $this->assertEquals($query, $this->setupBasicQueryFilter($query, [
                    'order_by'  => 'updated',
                    'direction' => 'desc',
        ]));

        $this->assertEquals($query, $this->setupBasicQueryFilter($query, [
                    'order_by'  => 'created',
                    'direction' => 'desc',
                    'columns'   => ['only' => 'created_at'],
        ]));
    }

    /**
     * Test \Antares\Support\Traits\QueryFilterTrait::setupBasicQueryFilter()
     * method when column should be excluded.
     *
     * @test
     */
    public function testSetupBasicQueryFilterMethodGivenColumnExcluded()
    {
        $query = m::mock('\Illuminate\Database\Query\Builder');

        $query->shouldReceive('orderBy')->never()->with('password', 'DESC')->andReturn($query);

        $this->assertEquals($query, $this->setupBasicQueryFilter($query, [
                    'order_by'  => 'password',
                    'direction' => 'desc',
                    'columns'   => ['except' => 'password'],
        ]));
    }

    /**
     * Test \Antares\Support\Traits\QueryFilterTrait::setupWildcardQueryFilter()
     * method.
     *
     * @test
     */
    public function testSetupWildcardQueryFilterMethod()
    {
        $query = m::mock('\Illuminate\Database\Query\Builder');

        $query->shouldReceive('where')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($query) {
                    $c($query);
                })
                ->shouldReceive('orWhere')->once()->with('name', 'LIKE', 'hello')
                ->shouldReceive('orWhere')->once()->with('name', 'LIKE', 'hello%')
                ->shouldReceive('orWhere')->once()->with('name', 'LIKE', '%hello')
                ->shouldReceive('orWhere')->once()->with('name', 'LIKE', '%hello%');

        $this->assertEquals($query, $this->setupWildcardQueryFilter($query, 'hello', ['name']));
    }

}

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

namespace Antares\Widgets\Model\Tests;

use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Antares\UI\UIComponents\Model\ComponentParams as Stub;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Connection;
use Antares\Testing\TestCase;
use Mockery as m;

class WidgetParamsTest extends TestCase
{

    use EloquentConnectionTrait;

    /**
     * Test Antares\Widgets\Model\WidgetParams::fillable params
     *
     * @test
     */
    public function testValidFillable()
    {
        $model = new Stub();
        $this->addMockConnection($model);
        $this->assertEquals($model->getFillable(), array('wid', 'uid', 'name', 'brand_id', 'resource', 'data'));
    }

    /**
     * Test Antares\Widgets\Model\WidgetParams::widgets() method.
     *
     * @test
     */
    public function testWidgets()
    {
        $model  = new Stub();
        $this->addMockConnection($model);
        $params = $model->widget();
        $this->assertSame('BelongsTo', class_basename($params));
    }

    /**
     * test updating tree structure
     * 
     * @test
     */
    public function testSaveTree()
    {
        $model = new Stub();
        $this->addMockConnection($model);

        $resolver   = m::mock(ConnectionResolverInterface::class);
        $model->setConnectionResolver($resolver);
        $resolver->shouldReceive('connection')->andReturn($connection = m::mock(Connection::class));
        $model->getConnection()->shouldReceive('getQueryGrammar')->andReturn($grammar    = m::mock(Grammar::class));
        $model->getConnection()->shouldReceive('getPostProcessor')->andReturn($processor  = m::mock(Processor::class));

        $grammar->shouldReceive('compileInsertGetId')->andReturn('');
        $grammar->shouldReceive('compileSelect')->once()->andReturn('INSERT INTO `tbl_widgets_params`(brand_id,resource,uid) VALUES(?,?,?)');
        $connection->shouldReceive('select')->once()->with('INSERT INTO `tbl_widgets_params`(brand_id,resource,uid) VALUES(?,?,?)', [1, '/', 1], true)->andReturn(null);
        $processor->shouldReceive('processInsertGetId')->andReturn(1);
        $processor->shouldReceive('processSelect')->once()->andReturn([]);
        $this->assertTrue($model->save([]));
    }

}

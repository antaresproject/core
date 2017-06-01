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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Antares\UI\UIComponents\Model\Components as Stub;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Antares\UI\UIComponents\Model\ComponentParams;
use Antares\UI\UIComponents\Model\ComponentTypes;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Connection;
use Antares\Testing\TestCase;
use Mockery as m;

class WidgetsTest extends TestCase
{

    use EloquentConnectionTrait;

    /**
     * test widgetTypes
     * 
     * @test
     */
    public function testComponentTypes()
    {
        $model = new Stub();
        $this->addMockConnection($model);
        $stub  = $model->widgetTypes();
        $this->assertInstanceOf(BelongsTo::class, $stub);
        $this->assertInstanceOf(ComponentTypes::class, $stub->getQuery()->getModel());
    }

    /**
     * test widgetParams method
     * 
     * @test
     */
    public function testComponentParams()
    {
        $model = new Stub();
        $this->addMockConnection($model);
        $stub  = $model->widgetParams();
        $this->assertInstanceOf(HasMany::class, $stub);
        $this->assertInstanceOf(ComponentParams::class, $stub->getQuery()->getModel());
    }

    /**
     * test save
     * 
     * @test
     */
    public function testSave()
    {
        $model = new Stub([
            'type_id'     => 1,
            'name'        => 'foo',
            'description' => 'foo',
            'status'      => 0
        ]);
        $this->addMockConnection($model);

        $resolver   = m::mock(ConnectionResolverInterface::class);
        $model->setConnectionResolver($resolver);
        $resolver->shouldReceive('connection')->andReturn($connection = m::mock(Connection::class));
        $model->getConnection()->shouldReceive('getQueryGrammar')->andReturn($grammar    = m::mock(Grammar::class));
        $model->getConnection()->shouldReceive('getPostProcessor')->andReturn($processor  = m::mock(Processor::class));

        $grammar->shouldReceive('compileInsertGetId')->andReturn('');

        $processor->shouldReceive('processInsertGetId')->andReturn(1);

        $this->assertTrue($model->save());
    }

}

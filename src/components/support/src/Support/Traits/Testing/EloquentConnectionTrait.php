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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Support\Traits\Testing;

use Mockery as m;
use Illuminate\Database\Eloquent\Model;

trait EloquentConnectionTrait
{

    /**
     * Set mock connection.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    protected function addMockConnection(Model $model)
    {
        $resolver   = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $model->setConnectionResolver($resolver);
        $connection = m::mock('\Illuminate\Database\Connection');
        $connection->shouldReceive('select')->withAnyArgs()->andReturnSelf();
        $connection->shouldReceive('insert')->withAnyArgs()->andReturnSelf();
        $connection->shouldReceive('delete')->withAnyArgs()->andReturnSelf();
        $resolver->shouldReceive('connection')->andReturn($connection);



        $grammar = m::mock('\Illuminate\Database\Query\Grammars\Grammar');
        $grammar->shouldReceive('compileSelect')
                ->withAnyArgs()
                ->andReturn(m::type('String'));
        $grammar->shouldReceive('compileInsert')
                ->withAnyArgs()
                ->andReturn(m::type('String'));
        $grammar->shouldReceive('compileDelete')
                ->withAnyArgs()
                ->andReturn(m::type('String'));

        $model->getConnection()
                ->shouldReceive('getQueryGrammar')
                ->andReturn($grammar);

        $processor = m::mock('\Illuminate\Database\Query\Processors\Processor');

        $processor->shouldReceive('processSelect')
                ->withAnyArgs()
                ->andReturn(m::any());

        $model->getConnection()
                ->shouldReceive('getPostProcessor')
                ->andReturn($processor);
    }

}

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

namespace Antares\Extension\TestCase;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Processors\Acl;
use Antares\Extension\Processors\Activator;
use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Extension\Repositories\ExtensionsRepository;
use Antares\Memory\Model\DeferedEvent;
use Mockery as m;

class ActivatorTest extends OperationSetupTestCase
{

    /**
     * @var \Mockery\MockInterface
     */
    protected $extensionsRepository;

    /**
     * @var \Mockery\MockInterface
     */
    protected $aclMigration;

    /**
     * @var \Mockery\MockInterface
     */
    protected $componentRepository;

    public function setUp() {
        parent::setUp();

        $this->extensionsRepository = m::mock(ExtensionsRepository::class);
        $this->aclMigration         = m::mock(Acl::class);
        $this->componentRepository  = m::mock(ComponentsRepository::class);
    }

    /**
     * @return Activator
     */
    public function getOperationProcessor() {
        return new Activator($this->container, $this->dispatcher, $this->kernel, $this->extensionsRepository, $this->aclMigration, $this->componentRepository);
    }

    public function testAsSuccess() {
        $processor = $this->getOperationProcessor();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationInfo')
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('operationSuccess')
            ->once()
            ->andReturnNull()
            ->getMock();

        $queryBuilder = m::mock(\Illuminate\Database\Eloquent\Builder::class)
            ->shouldReceive('firstOrCreate')
            ->with(m::type('array'))
            ->once()
            ->andReturnNull()
            ->getMock();

        $deferedEvent = m::mock('overload:' . DeferedEvent::class);
        $deferedEvent->shouldReceive('query')->once()->andReturn($queryBuilder)->getMock();

        $this->app->instance(DeferedEvent::class, $deferedEvent);

        $name = 'foo/bar';
        $extension = $this->buildExtensionMock($name)
            ->shouldReceive('getPath')
            ->andReturn('/src/component/foo/bar')
            ->getMock();

        $this->dispatcher->shouldReceive('fire')->twice()->andReturnNull()->getMock();
        $this->aclMigration->shouldReceive('import')->once()->with($handler, $extension)->andReturnNull()->getMock();

        $this->extensionsRepository->shouldReceive('save')->once()->with($extension, [
            'status' => ExtensionContract::STATUS_ACTIVATED,
        ])->andReturnNull()->getMock();

        $processor->run($handler, $extension);
    }

    public function testWithException() {
        $processor = $this->getOperationProcessor();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationInfo')
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('operationFailed')
            ->once()
            ->andReturnNull()
            ->getMock();

        $name = 'foo/bar';
        $extension = $this->buildExtensionMock($name)
            ->shouldReceive('getPath')
            ->andReturn('/src/component/foo/bar')
            ->getMock();

        $this->app['log']  = m::mock(\Psr\Log\LoggerInterface::class)->shouldReceive('error')->once()->withAnyArgs()->getMock();

        $this->dispatcher->shouldReceive('fire')->twice()->andReturnNull()->getMock();
        $this->aclMigration->shouldReceive('import')->once()->with($handler, $extension)->andThrow(\Exception::class)->getMock();

        $processor->run($handler, $extension);
    }

}

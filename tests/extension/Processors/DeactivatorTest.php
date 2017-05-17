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

use Antares\Acl\Migration;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Processors\Acl;
use Antares\Extension\Processors\Deactivator;
use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Extension\Repositories\ExtensionsRepository;
use Mockery as m;

class DeactivatorTest extends OperationSetupTestCase
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
        $this->aclMigration         = m::mock(Migration::class);
        $this->componentRepository  = m::mock(ComponentsRepository::class);
    }

    /**
     * @return Deactivator
     */
    public function getOperationProcessor() {
        return new Deactivator($this->container, $this->dispatcher, $this->kernel, $this->extensionsRepository, $this->aclMigration, $this->componentRepository);
    }

    public function testAsSuccess() {
        $processor = $this->getOperationProcessor();

        $this->componentRepository
            ->shouldReceive('isRequired')
            ->once()
            ->andReturn(false)
            ->getMock();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationInfo')
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('operationSuccess')
            ->once()
            ->andReturnNull()
            ->getMock();

        $name = 'foo/bar';
        $extension = $this->buildExtensionMock($name)
            ->shouldReceive('getPath')
            ->andReturn('/src/component/foo/bar')
            ->getMock();

        $this->dispatcher->shouldReceive('fire')->twice()->andReturnNull()->getMock();
        $this->aclMigration->shouldReceive('down')->once()->with($name)->andReturnNull()->getMock();

        $this->extensionsRepository->shouldReceive('save')->once()->with($extension, [
            'status' => ExtensionContract::STATUS_INSTALLED,
        ])->andReturnNull()->getMock();

        $processor->run($handler, $extension);
    }

    public function testWithException() {
        $processor = $this->getOperationProcessor();

        $this->componentRepository
            ->shouldReceive('isRequired')
            ->once()
            ->andReturn(false)
            ->getMock();

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
        $this->aclMigration->shouldReceive('down')->once()->with($name)->andThrow(\Exception::class)->getMock();

        $processor->run($handler, $extension);
    }

    public function testWithExceptionForRequiredComponent() {
        $processor = $this->getOperationProcessor();

        $this->componentRepository
            ->shouldReceive('isRequired')
            ->once()
            ->andReturn(true)
            ->getMock();

        $handler = $this->buildOperationHandlerMock()
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

        $this->dispatcher->shouldReceive('fire')->once()->andReturnNull()->getMock();
        $this->aclMigration->shouldReceive('down')->never()->getMock();

        $processor->run($handler, $extension);
    }

}

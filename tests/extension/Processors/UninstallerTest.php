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
use Antares\Extension\Composer\Handler as ComposerHandler;
use Antares\Extension\Processors\Uninstaller;
use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Extension\Repositories\ExtensionsRepository;
use Antares\Publisher\AssetManager;
use Antares\Publisher\MigrateManager;
use Mockery as m;
use Symfony\Component\Process\Process;

class UninstallerTest extends OperationSetupTestCase
{

    /**
     * @var \Mockery\MockInterface
     */
    protected $composerHandler;

    /**
     * @var \Mockery\MockInterface
     */
    protected $extensionsRepository;

    /**
     * @var \Mockery\MockInterface
     */
    protected $migrateManager;

    /**
     * @var \Mockery\MockInterface
     */
    protected $assetManager;

    /**
     * @var \Mockery\MockInterface
     */
    protected $componentRepository;

    public function setUp() {
        parent::setUp();

        $this->composerHandler      = m::mock(ComposerHandler::class);
        $this->extensionsRepository = m::mock(ExtensionsRepository::class);
        $this->migrateManager       = m::mock(MigrateManager::class);
        $this->assetManager         = m::mock(AssetManager::class);
        $this->componentRepository  = m::mock(ComponentsRepository::class);

        $this->container->shouldReceive('make')->once()->with('antares.publisher.migrate')->andReturn($this->migrateManager)->getMock();
        $this->container->shouldReceive('make')->once()->with('antares.publisher.asset')->andReturn($this->assetManager)->getMock();
    }

    /**
     * @return Uninstaller
     */
    public function getOperationProcessor() {
        return new Uninstaller($this->composerHandler, $this->container, $this->dispatcher, $this->kernel, $this->extensionsRepository, $this->componentRepository);
    }

    public function testWithoutComposerAsSuccess() {
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
        $this->composerHandler->shouldReceive('run')->never()->andReturnNull()->getMock();
        $this->migrateManager->shouldReceive('uninstall')->once()->with($name)->andReturnNull()->getMock();
        $this->assetManager->shouldReceive('delete')->once()->with(str_replace('/', '_', $name))->andReturnNull()->getMock();

        $this->extensionsRepository->shouldReceive('save')->once()->with($extension, [
            'status'    => ExtensionContract::STATUS_AVAILABLE,
            'options'   => []
        ])->andReturnNull()->getMock();

        $processor->run($handler, $extension);
    }

    public function testWithComposerAsSuccess() {
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
            ->getMock()
            ->shouldReceive('setSettings')
            ->andReturnNull()
            ->getMock();

        $this->dispatcher->shouldReceive('fire')->twice()->andReturnNull()->getMock();
        $this->migrateManager->shouldReceive('uninstall')->once()->with($name)->andReturnNull()->getMock();
        $this->assetManager->shouldReceive('delete')->once()->with(str_replace('/', '_', $name))->andReturnNull()->getMock();

        $process = m::mock(Process::class)
            ->shouldReceive('stop')
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('isSuccessful')
            ->andReturn(true)
            ->getMock();

        $this->composerHandler->shouldReceive('run')
            ->withAnyArgs()
            ->andReturn($process)
            ->getMock();

        $this->extensionsRepository->shouldReceive('save')->once()->with($extension, [
            'status'    => ExtensionContract::STATUS_AVAILABLE,
            'options'   => []
        ])->andReturnNull()->getMock();

        $processor->run($handler, $extension, ['purge']);
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
            ->getMock()
            ->shouldReceive('operationSuccess')
            ->never()
            ->andReturnNull()
            ->getMock();

        $name = 'foo/bar';
        $extension = $this->buildExtensionMock($name)
            ->shouldReceive('getPath')
            ->andReturn('/src/component/foo/bar')
            ->getMock()
            ->shouldReceive('setSettings')
            ->andReturnNull()
            ->getMock();

        $this->app['log']  = m::mock(\Psr\Log\LoggerInterface::class)->shouldReceive('error')->once()->withAnyArgs()->getMock();

        $this->dispatcher->shouldReceive('fire')->twice()->andReturnNull()->getMock();
        $this->migrateManager->shouldReceive('uninstall')->once()->with($name)->andThrow(\Exception::class)->getMock();

        $processor->run($handler, $extension, ['purge']);
    }

    public function testWithExceptionInComposer() {
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
            ->getMock()
            ->shouldReceive('setSettings')
            ->andReturnNull()
            ->getMock();

        $this->dispatcher->shouldReceive('fire')->twice()->andReturnNull()->getMock();
        $this->migrateManager->shouldReceive('uninstall')->once()->with($name)->andReturnNull()->getMock();
        $this->assetManager->shouldReceive('delete')->once()->with(str_replace('/', '_', $name))->andReturnNull()->getMock();

        $process = m::mock(Process::class)
            ->shouldReceive('stop')
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('isSuccessful')
            ->andReturn(false)
            ->getMock();

        $this->composerHandler->shouldReceive('run')
            ->withAnyArgs()
            ->andReturn($process)
            ->getMock();

        $this->extensionsRepository->shouldReceive('save')->once()->with($extension, [
            'status'    => ExtensionContract::STATUS_AVAILABLE,
            'options'   => []
        ])->andReturnNull()->getMock();

        $this->app['log']  = m::mock(\Psr\Log\LoggerInterface::class)->shouldReceive('error')->once()->withAnyArgs()->getMock();

        $processor->run($handler, $extension, ['purge']);
    }

    public function testWithExceptionForRequiredComponent() {
        $processor = $this->getOperationProcessor();

        $this->componentRepository
            ->shouldReceive('isRequired')
            ->once()
            ->andReturn(true)
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
            ->getMock()
            ->shouldReceive('setSettings')
            ->andReturnNull()
            ->getMock();

        $this->app['log']  = m::mock(\Psr\Log\LoggerInterface::class)->shouldReceive('error')->once()->withAnyArgs()->getMock();

        $this->dispatcher->shouldReceive('fire')->once()->andReturnNull()->getMock();
        $this->migrateManager->shouldReceive('uninstall')->never()->getMock();
        $this->assetManager->shouldReceive('delete')->never()->getMock();
        $this->extensionsRepository->shouldReceive('save')->never()->getMock();

        $processor->run($handler, $extension, ['purge']);
    }

}

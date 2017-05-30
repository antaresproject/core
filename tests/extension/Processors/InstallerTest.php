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

use Antares\Extension\Contracts\Config\SettingsContract;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Factories\SettingsFactory;
use Antares\Extension\Processors\Installer;
use Antares\Extension\Composer\Handler as ComposerHandler;
use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Extension\Repositories\ExtensionsRepository;
use Antares\Extension\Validators\ExtensionValidator;
use Antares\Publisher\AssetManager;
use Antares\Publisher\MigrateManager;
use Mockery as m;
use Symfony\Component\Process\Process;

class InstalledTest extends OperationSetupTestCase
{

    /**
     * @var \Mockery\MockInterface
     */
    protected $composerHandler;

    /**
     * @var \Mockery\MockInterface
     */
    protected $extensionValidator;

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
    protected $settingsFactory;

    /**
     * @var \Mockery\MockInterface
     */
    protected $componentsRepository;

    public function setUp() {
        parent::setUp();

        $this->composerHandler      = m::mock(ComposerHandler::class);
        $this->extensionValidator   = m::mock(ExtensionValidator::class);
        $this->extensionsRepository = m::mock(ExtensionsRepository::class);
        $this->migrateManager       = m::mock(MigrateManager::class);
        $this->assetManager         = m::mock(AssetManager::class);
        $this->settingsFactory      = m::mock(SettingsFactory::class);
        $this->componentsRepository = m::mock(ComponentsRepository::class);

        $this->container->shouldReceive('make')->once()->with('antares.publisher.migrate')->andReturn($this->migrateManager)->getMock();
        $this->container->shouldReceive('make')->once()->with('antares.publisher.asset')->andReturn($this->assetManager)->getMock();
    }

    /**
     * @return Installer
     */
    public function getOperationProcessor() {
        return new Installer($this->composerHandler, $this->extensionValidator, $this->container, $this->dispatcher, $this->kernel, $this->extensionsRepository, $this->settingsFactory, $this->componentsRepository);
    }

    public function testWithoutComposerAsSuccess() {
        $processor = $this->getOperationProcessor();

        $this->componentsRepository
            ->shouldReceive('isRequired')
            ->once()
            ->andReturn(false)
            ->getMock();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationInfo')
            ->twice()
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
        $this->extensionValidator->shouldReceive('validateAssetsPath')->once()->with($extension)->andReturnNull()->getMock();
        $this->composerHandler->shouldReceive('run')->never()->andReturnNull()->getMock();
        $this->migrateManager->shouldReceive('extension')->once()->with($name)->andReturnNull()->getMock();
        $this->assetManager->shouldReceive('extension')->once()->with(str_replace('/', '_', $name))->andReturnNull()->getMock();

        $this->extensionsRepository->shouldReceive('save')->once()->with($extension, [
            'status'    => ExtensionContract::STATUS_INSTALLED,
            'options'   => $extension->getSettings()->getData(),
            'required'  => false,
        ])->andReturnNull()->getMock();

        $settings = m::mock(SettingsContract::class);

        $this->settingsFactory->shouldReceive('createFromConfig')
            ->once()
            ->with('/src/component/foo/bar/resources/config/settings.php')
            ->andReturn($settings)
            ->getMock();

        $processor->run($handler, $extension, ['skip-composer']);
    }

    public function testWithFailedAssetsValidation() {
        $processor = $this->getOperationProcessor();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationInfo')
            ->once()
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('operationFailed')
            ->once()
            ->andReturnNull()
            ->getMock();

        $name = 'foo/bar';
        $extension = $this->buildExtensionMock($name);

        $this->dispatcher->shouldReceive('fire')->twice()->andReturnNull()->getMock();
        $this->extensionValidator->shouldReceive('validateAssetsPath')->once()->with($extension)->andThrow(ExtensionException::class)->getMock();

        $processor->run($handler, $extension, ['skip-composer']);
    }

    public function testWithComposerAsSuccess() {
        $processor = $this->getOperationProcessor();

        $this->componentsRepository
            ->shouldReceive('isRequired')
            ->once()
            ->andReturn(true)
            ->getMock();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationInfo')
            ->twice()
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
        $this->extensionValidator->shouldReceive('validateAssetsPath')->once()->with($extension)->andReturnNull()->getMock();
        $this->migrateManager->shouldReceive('extension')->once()->with($name)->andReturnNull()->getMock();
        $this->assetManager->shouldReceive('extension')->once()->with(str_replace('/', '_', $name))->andReturnNull()->getMock();
        $this->componentsRepository->shouldReceive('getTargetBranch')->with($name)->andReturn(m::type('string'))->getMock();

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
            'status'    => ExtensionContract::STATUS_INSTALLED,
            'options'   => $extension->getSettings()->getData(),
            'required'  => true,
        ])->andReturnNull()->getMock();

        $settings = m::mock(SettingsContract::class);

        $this->settingsFactory->shouldReceive('createFromConfig')
            ->once()
            ->with('/src/component/foo/bar/resources/config/settings.php')
            ->andReturn($settings)
            ->getMock();

        $processor->run($handler, $extension);
    }

}

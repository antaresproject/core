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

use Antares\Extension\Collections\Extensions;
use Antares\Extension\Factories\SettingsFactory;
use Antares\Extension\FilesystemFinder;
use Antares\Extension\Manager;
use Antares\Extension\Model\Extension;
use Antares\Extension\Model\ExtensionModel;
use Antares\Extension\Repositories\ExtensionsRepository;
use Antares\Support\Collection;
use Composer\Package\CompletePackageInterface;
use Illuminate\Filesystem\Filesystem;
use Antares\Testbench\TestCase;
use Mockery as m;

class ManagerTest extends TestCase
{

    /**
     * @var \Mockery\MockInterface
     */
    protected $application;

    /**
     * @var \Mockery\MockInterface
     */
    protected $configRepository;

    /**
     * @var \Mockery\MockInterface
     */
    protected $filesystemFinder;

    /**
     * @var \Mockery\MockInterface
     */
    protected $extensionsRepository;

    /**
     * @var \Mockery\MockInterface
     */
    protected $filesystem;

    /**
     * @var \Mockery\MockInterface
     */
    protected $settingsFactory;

    /**
     * @var string
     */
    protected $providersPath = '';

    public function setUp()
    {
        parent::setUp();

        $this->app['antares.installed'] = true;

        $this->filesystemFinder     = m::mock(FilesystemFinder::class);
        $this->extensionsRepository = m::mock(ExtensionsRepository::class);
        $this->filesystem           = m::mock(Filesystem::class);
        $this->settingsFactory      = m::mock(SettingsFactory::class);
    }

    /**
     * @return Manager
     */
    protected function getManagerInstance()
    {
        return new Manager($this->filesystemFinder, $this->extensionsRepository, $this->filesystem, $this->settingsFactory);
    }

    public function testEmptyAvailableExtensions()
    {
        $foundExtensions = new Extensions();

        $this->filesystemFinder
                ->shouldReceive('findExtensions')
                ->once()
                ->andReturn($foundExtensions)
                ->getMock();

        $this->extensionsRepository
                ->shouldReceive('all')
                ->once()
                ->andReturn([]);

        $this->assertCount(0, $this->getManagerInstance()->getAvailableExtensions());
    }

    public function testIfAvailableExtensionsAreExecutedOnce()
    {
        $foundExtensions = new Extensions();

        $this->filesystemFinder
                ->shouldReceive('findExtensions')
                ->once()
                ->andReturn($foundExtensions)
                ->getMock();

        $this->extensionsRepository
                ->shouldReceive('all')
                ->once()
                ->andReturn([]);

        $manager = $this->getManagerInstance();
        $manager->getAvailableExtensions();
        $manager->getAvailableExtensions();
    }

    public function testAvailableExtensionsWithModels()
    {
        $package1 = m::mock(CompletePackageInterface::class)
                ->shouldReceive('getName')
                ->andReturn('antaresproject/component-example')
                ->getMock();

        $package2 = m::mock(CompletePackageInterface::class)
                ->shouldReceive('getName')
                ->andReturn('antaresproject/component-testable')
                ->getMock();

        $extensionWithModel    = new Extension($package1, '/path/to/component/example', '\\Antares\\Example');
        $extensionWithoutModel = new Extension($package2, '/path/to/component/testable', '\\Antares\\Testable');

        $options = ['testable' => 'values'];

        $model = m::mock(ExtensionModel::class)
                ->shouldReceive('getFullName')
                ->andReturn('antaresproject/component-example')
                ->shouldReceive('getStatus')
                ->andReturn(2)
                ->shouldReceive('getOptions')
                ->andReturn($options)
                ->shouldReceive('isRequired')
                ->andReturn(false)
                ->getMock();

        $this->app->instance(ExtensionModel::class, $model);

        $storedExtensions = new Collection([$model]);

        $foundExtensions = new Extensions([
            $extensionWithModel,
            $extensionWithoutModel,
        ]);

        $this->filesystemFinder
                ->shouldReceive('findExtensions')
                ->once()
                ->andReturn($foundExtensions)
                ->getMock();

        $this->extensionsRepository
                ->shouldReceive('all')
                ->once()
                ->andReturn($storedExtensions);

        $this->filesystem
                ->shouldReceive('exists')
                ->withAnyArgs()
                ->andReturn(false)
                ->getMock();

        $availableExtensions = $this->getManagerInstance()->getAvailableExtensions();

        $this->assertCount(2, $availableExtensions->all());

        $withModel = $availableExtensions->findByName('antaresproject/component-example');

        $this->assertSame(2, $withModel->getStatus());
        $this->assertEquals($options, $withModel->getSettings()->getData());
    }

}

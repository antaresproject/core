<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Extension\TestCase;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Loader;
use Antares\Extension\Manager;
use Antares\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;

class LoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Mockery\MockInterface
     */
    protected $application;

    /**
     * @var \Mockery\MockInterface
     */
    protected $manager;

    /**
     * @var \Mockery\MockInterface
     */
    protected $filesystem;

    /**
     * @var \Mockery\MockInterface
     */
    protected $extension;

    /**
     * @var string
     */
    protected $providersPath = '';

    public function setUp() {
        parent::setUp();

        $this->application  = m::mock(Application::class);
        $this->manager      = m::mock(Manager::class);
        $this->filesystem   = m::mock(Filesystem::class);

        $extensionPath          = '/dummy/path';
        $this->providersPath    = $extensionPath . '/providers.php';

        $this->extension = m::mock(ExtensionContract::class)
            ->shouldReceive('getPath')
            ->once()
            ->andReturn($extensionPath)
            ->getMock();
    }

    public function tearDown() {
        parent::tearDown();
        m::close();
    }

    /**
     * @return Loader
     */
    protected function getLoaderInstance() {
        return new Loader($this->application, $this->manager, $this->filesystem);
    }

    public function testRegisterWithoutProviders() {
        $this->filesystem
            ->shouldReceive('exists')
            ->once()
            ->with($this->providersPath)
            ->andReturn(false);

        $this->getLoaderInstance()->register($this->extension);
    }

    public function testRegisterWithOneProvider() {
        $providers = [
            'path/to/class/one',
        ];

        $this->filesystem
            ->shouldReceive('exists')
            ->once()
            ->with($this->providersPath)
            ->andReturn(true)
            ->getMock()
            ->shouldReceive('getRequire')
            ->once()
            ->with($this->providersPath)
            ->andReturn($providers)
            ->getMock();

        $serviceProviderInstance = m::mock(ServiceProvider::class)
            ->shouldReceive('isDeferred')
            ->once()
            ->andReturn(false)
            ->getMock();

        $this->application
            ->shouldReceive('resolveProviderClass')
            ->once()
            ->with($providers[0])
            ->andReturn($serviceProviderInstance)
            ->getMock()
            ->shouldReceive('register')
            ->once()
            ->with($serviceProviderInstance)
            ->andReturn($serviceProviderInstance)
            ->getMock();

        $this->getLoaderInstance()->register($this->extension);
    }

    public function testRegisterWithManyProvider() {
        $providers = [
            'path/to/class/one',
            'path/to/class/two',
            'path/to/class/three',
        ];

        $this->filesystem
            ->shouldReceive('exists')
            ->once()
            ->with($this->providersPath)
            ->andReturn(true)
            ->getMock()
            ->shouldReceive('getRequire')
            ->once()
            ->with($this->providersPath)
            ->andReturn($providers)
            ->getMock();

        $serviceProviderInstance = m::mock(ServiceProvider::class)
            ->shouldReceive('isDeferred')
            ->times(3)
            ->andReturn(false)
            ->getMock();

        foreach($providers as $provider) {
            $this->application
                ->shouldReceive('resolveProviderClass')
                ->once()
                ->with($provider)
                ->andReturn($serviceProviderInstance)
                ->getMock();
        }

        $this->application
            ->shouldReceive('register')
            ->times(3)
            ->with($serviceProviderInstance)
            ->andReturn($serviceProviderInstance)
            ->getMock();

        $this->getLoaderInstance()->register($this->extension);
    }

    public function testRegisterAsDeferred() {
        $providers = [
            'path/to/class/one',
        ];

        $this->filesystem
            ->shouldReceive('exists')
            ->once()
            ->with($this->providersPath)
            ->andReturn(true)
            ->getMock()
            ->shouldReceive('getRequire')
            ->once()
            ->with($this->providersPath)
            ->andReturn($providers)
            ->getMock();

        $serviceProviderInstance = m::mock(ServiceProvider::class)
            ->shouldReceive('isDeferred')
            ->once()
            ->andReturn(true)
            ->getMock()
            ->shouldReceive('provides')
            ->once()
            ->andReturn(m::type('array'))
            ->getMock();

        $services = [];

        $this->application
            ->shouldReceive('resolveProviderClass')
            ->once()
            ->with($providers[0])
            ->andReturn($serviceProviderInstance)
            ->getMock()
            ->shouldReceive('getDeferredServices')
            ->once()
            ->andReturn($services)
            ->shouldReceive('setDeferredServices')
            ->once()
            ->with($services)
            ->andReturnNull()
            ->getMock();

        $this->getLoaderInstance()->register($this->extension);
    }

}
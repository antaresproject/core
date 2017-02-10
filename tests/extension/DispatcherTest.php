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

use Mockery as m;
use Antares\Extension\Dispatcher;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Get mocked Antares\Extension\ProviderRepository.
     *
     * @return \Antares\Extension\ProviderRepository
     */
    protected function getProvider()
    {
        return m::mock('\Antares\Extension\ProviderRepository', [
            m::mock('\Illuminate\Contracts\Foundation\Application'),
        ]);
    }

    /**
     * Test Antares\Extension\Dispatcher::start() method.
     *
     * @test
     */
    public function testStartMethod()
    {
        $provider = $this->getProvider();
        $config   = m::mock('\Illuminate\Contracts\Config\Repository');
        $event    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $files    = m::mock('\Illuminate\Filesystem\Filesystem');
        $finder   = m::mock('\Antares\Extension\Finder');

        $options1 = [
            'config'      => ['handles' => 'laravel'],
            'path'        => '/foo/app/laravel/framework/',
            'source-path' => '/foo/app',
            'autoload'    => [
                'source-path::hello.php',
                'start.php',
            ],
            'provides'    => ['Laravel\FrameworkServiceProvider'],
        ];

        $options2 = [
            'config' => [],
            'path'   => '/foo/app/',
        ];

        $config->shouldReceive('set')->once()
                ->with('antares/extension::handles.laravel/framework', 'laravel')->andReturnNull();
        $event->shouldReceive('fire')->once()
                ->with('extension.started: laravel/framework', [$options1])->andReturnNull()
            ->shouldReceive('fire')->once()
                ->with('extension.started', ['laravel/framework', $options1])->andReturnNull()
            ->shouldReceive('fire')->once()
                ->with('extension.booted: laravel/framework', [$options1])->andReturnNull()
            ->shouldReceive('fire')->once()
                ->with('extension.booted', ['laravel/framework', $options1])->andReturnNull();
        $files->shouldReceive('isFile')->once()->with('/foo/app/hello.php')->andReturn(true)
            ->shouldReceive('isFile')->once()->with('/foo/app/start.php')->andReturn(true)
            ->shouldReceive('isFile')->once()->with('/foo/app/src/antares.php')->andReturn(true)
            ->shouldReceive('isFile')->once()->with('/foo/app/antares.php')->andReturn(false)
            ->shouldReceive('getRequire')->once()->with('/foo/app/hello.php')->andReturn(true)
            ->shouldReceive('getRequire')->once()->with('/foo/app/start.php')->andReturn(true)
            ->shouldReceive('getRequire')->once()->with('/foo/app/src/antares.php')->andReturn(true);
        $provider->shouldReceive('provides')->once()
                ->with(['Laravel\FrameworkServiceProvider'])->andReturn(true);

        $event->shouldReceive('fire')->once()
                ->with('extension.started: app', [$options2])->andReturnNull()
            ->shouldReceive('fire')->once()
                ->with('extension.started', ['app', $options2])->andReturnNull()
            ->shouldReceive('fire')->once()
                ->with('extension.booted: app', [$options2])->andReturnNull()
            ->shouldReceive('fire')->once()
                ->with('extension.booted', ['app', $options2])->andReturnNull();
        $files->shouldReceive('isFile')->once()
                ->with('/foo/app/src/antares.php')->andReturn(false)
            ->shouldReceive('isFile')->once()
                ->with('/foo/app/antares.php')->andReturn(true)
            ->shouldReceive('getRequire')->once()
                ->with('/foo/app/antares.php')->andReturn(true);

        $finder->shouldReceive('resolveExtensionPath')->andReturnUsing(function ($p) {
            return $p;
        });

        $stub = new Dispatcher($config, $event, $files, $finder, $provider);

        $stub->register('laravel/framework', $options1);
        $stub->register('app', $options2);
        $stub->boot();
    }

    /**
     * Test Antares\Extension\Dispatcher::finish() method.
     *
     * @test
     */
    public function testFinishMethod()
    {
        $config   = m::mock('\Illuminate\Contracts\Config\Repository');
        $event    = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $files    = m::mock('\Illuminate\Filesystem\Filesystem');
        $finder   = m::mock('\Antares\Extension\Finder');

        $event->shouldReceive('fire')->once()
                ->with('extension.done: laravel/framework', [['foo']])
                ->andReturnNull()
            ->shouldReceive('fire')->once()
                ->with('extension.done', ['laravel/framework', ['foo']])
                ->andReturnNull();

        $stub = new Dispatcher($config, $event, $files, $finder, $this->getProvider());
        $stub->finish('laravel/framework', ['foo']);
    }
}

<?php

namespace Antares\Extension\TestCase;

use Antares\Extension\Contracts\ExtensionContract;
use Mockery as m;
use Illuminate\Support\ServiceProvider;
use Antares\Extension\Loader;

class ProviderRepositoryTest extends \PHPUnit_Framework_TestCase
{

    public function testRegisterExtensionProviders()
    {
        $service      = 'Antares\Extension\TestCase\FooServiceProvider';
        $manifestPath = '/var/www/laravel/bootstrap/cache';

        $mock   = m::mock($service);
        $app    = m::mock('\Antares\Foundation\Application');
        $events = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $files  = m::mock('\Illuminate\Filesystem\Filesystem');

        $app->shouldReceive('getCachedExtensionServicesPath')->once()->andReturn("{$manifestPath}/extension.php")->getMock();

        $extensionPath = '/dummy/path';
        $providersPath = $extensionPath . '/providers.php';

        $extension = m::mock(ExtensionContract::class)
                ->shouldReceive('getPath')
                ->once()
                ->andReturn($extensionPath)
                ->getMock();

        $files
                ->shouldReceive('exists')
                ->once()
                ->with($providersPath)
                ->andReturn(false);

        $stub = new Loader($app, $events, $files);

        $stub->registerExtensionProviders($extension);
    }

    /**
     * Test Orchestra\Extension\ProviderRepository::provides()
     * method.
     */
    public function testServicesMethodWhenEager()
    {
        $service      = 'Antares\Extension\TestCase\FooServiceProvider';
        $manifestPath = '/var/www/laravel/bootstrap/cache';

        $mock   = m::mock($service);
        $app    = m::mock('\Antares\Foundation\Application');
        $events = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $files  = m::mock('\Illuminate\Filesystem\Filesystem');

        $schema = [
            'eager'    => true,
            'when'     => [],
            'deferred' => [],
        ];

        $app->shouldReceive('getCachedExtensionServicesPath')->once()->andReturn("{$manifestPath}/extension.php")
                ->shouldReceive('resolveProvider')->once()
                ->with($service)->andReturn($mock)
                ->shouldReceive('register')->once()->with($mock)->andReturn($mock);
        $files->shouldReceive('exists')->once()
                ->with("{$manifestPath}/extension.php")->andReturn(false)
                ->shouldReceive('put')->once()
                ->with("{$manifestPath}/extension.php", '<?php return ' . var_export([$service => $schema], true) . ';')
                ->andReturnNull();

        $mock->shouldReceive('isDeferred')->once()->andReturn(!$schema['eager']);

        $stub = new Loader($app, $events, $files);
        $stub->loadManifest();
        $stub->provides([$service]);

        $this->assertTrue($stub->shouldRecompile());
        $this->assertNull($stub->writeManifest());
    }

    /**
     * Test Orchestra\Extension\ProviderRepository::provides()
     * method.
     */
    public function testServicesMethodWhenDeferred()
    {
        $service      = 'Antares\Extension\TestCase\FooServiceProvider';
        $manifestPath = '/var/www/laravel/bootstrap/cache';

        $mock   = m::mock($service);
        $app    = m::mock('\Antares\Foundation\Application');
        $events = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $files  = m::mock('\Illuminate\Filesystem\Filesystem');

        $schema = [
            'eager'    => false,
            'when'     => [],
            'deferred' => [
                'foo' => $service,
            ],
        ];

        $app->shouldReceive('getCachedExtensionServicesPath')->once()->andReturn("{$manifestPath}/extension.php")
                ->shouldReceive('resolveProvider')->once()
                ->with($service)->andReturn($mock)
                ->shouldReceive('addDeferredServices')->once()->andReturn([
            'foo' => $service,
        ]);
        $files->shouldReceive('exists')->once()
                ->with("{$manifestPath}/extension.php")->andReturn(false)
                ->shouldReceive('put')->once()
                ->with("{$manifestPath}/extension.php", '<?php return ' . var_export([$service => $schema], true) . ';')
                ->andReturnNull();

        $mock->shouldReceive('isDeferred')->once()->andReturn(!$schema['eager'])
                ->shouldReceive('provides')->once()->andReturn(array_keys($schema['deferred']))
                ->shouldReceive('when')->once()->andReturn($schema['when']);

        $stub = new Loader($app, $events, $files);
        $stub->loadManifest();
        $stub->provides([$service]);

        $this->assertTrue($stub->shouldRecompile());
        $this->assertNull($stub->writeManifest());
    }

    /**
     * Test Orchestra\Extension\ProviderRepository::provides()
     * method.
     */
    public function testServicesMethodWhenManifestExists()
    {
        $service      = 'Antares\Extension\TestCase\FooServiceProvider';
        $manifestPath = '/var/www/laravel/bootstrap/cache';

        $mock         = m::mock($service);
        $app          = m::mock('\Antares\Foundation\Application');
        $events       = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $files        = m::mock('\Illuminate\Filesystem\Filesystem');
        $manifestPath = '/var/www/laravel/bootstrap/cache';

        $schema = [
            'eager'    => true,
            'when'     => [],
            'deferred' => [],
        ];

        $app->shouldReceive('getCachedExtensionServicesPath')->once()->andReturn("{$manifestPath}/extension.php")
                ->shouldReceive('register')->once()->with($service)->andReturnNull();
        $files->shouldReceive('exists')->once()->with("{$manifestPath}/extension.php")->andReturn(true)
                ->shouldReceive('getRequire')->once()->with("{$manifestPath}/extension.php")
                ->andReturn([$service => $schema]);

        $stub = new Loader($app, $events, $files);
        $stub->loadManifest();
        $stub->provides([$service]);

        $this->assertFalse($stub->shouldRecompile());
        $this->assertNull($stub->writeManifest());
    }

}

class FooServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function when()
    {
        return [];
    }

}

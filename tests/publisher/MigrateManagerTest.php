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

namespace Antares\Publisher\TestCase;

use Antares\Extension\ExtensionServiceProvider;
use Antares\Testbench\ApplicationTestCase;
use Antares\Publisher\MigrateManager;
use Mockery as m;

class MigrateManagerTest extends ApplicationTestCase
{

    public function setUp()
    {
        parent::setUp();

        $app           = $this->app;
        $app['events'] = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $files         = m::mock('\Illuminate\Filesystem\Filesystem');
        $files->shouldReceive('isDirectory')->with("/foo/path/src/core/src/components/memory/resources/database/migrations/")->andReturn(false);
        $app['files']  = $files;

// Only for tests
        $app['antares.installed'] = false;

        $extensionStub = new ExtensionServiceProvider($app);
        $extensionStub->register();
    }

    /**
     * Test Antares\Publisher\MigrateManager::run() method.
     *
     * @test
     */
    public function testRunMethod()
    {
        $migrator   = m::mock('\Illuminate\Database\Migrations\Migrator');
        $repository = m::mock('\Illuminate\Database\Migrations\DatabaseMigrationRepository');
        $seeder     = m::mock('\Illuminate\Database\Seeder');


        $migrator->shouldReceive('getRepository')->once()->andReturn($repository)
                ->shouldReceive('getMigrationFiles')->once()->with("/foo/path/migrations")->andReturn([])
                ->shouldReceive('run')->once()->with('/foo/path/migrations')->andReturn(null);

        $repository->shouldReceive('repositoryExists')->once()->andReturn(false)
                ->shouldReceive('createRepository')->once()->andReturn(null)
                ->shouldReceive('getRan')->once()->andReturn(null);

        $stub = new MigrateManager($this->app, $migrator, $seeder);
        $stub->run('/foo/path/migrations');
    }

    /**
     * Test Antares\Publisher\MigrateManager::extension() method.
     *
     * @test
     */
    public function testExtensionMethod()
    {
        $app = $this->app;

        $app['migrator']                 = $migrator                        = m::mock('\Illuminate\Database\Migrations\Migrator');
        $app['files']                    = $files                           = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['antares.extension']        = $extension                       = m::mock('\Antares\Extension\Factory');
        $app['antares.extension.finder'] = $finder                          = m::mock('\Antares\Extension\Finder');



        $repository = m::mock('\Illuminate\Database\Migrations\DatabaseMigrationRepository');

        $extension->shouldReceive('option')->twice()->with('foo/bar', 'path')->andReturn('/foo/path/foo/bar/')
                ->shouldReceive('option')->twice()->with('foo/bar', 'source-path')->andReturn('/foo/app/foo/bar/')
                ->shouldReceive('option')->twice()->with('laravel/framework', 'path')->andReturn('/foo/path/laravel/framework/')
                ->shouldReceive('option')->twice()->with('laravel/framework', 'source-path')->andReturn('/foo/path/laravel/framework/')
                ->shouldReceive('fill')->times(4)->andReturnSelf();


        $finder->shouldReceive('getPathFromExtensionName')->twice()->with('/foo/path/foo/bar')->andReturn('/foo/path/foo/bar')
                ->shouldReceive('getPathFromExtensionName')->twice()->with('/foo/app/foo/bar')->andReturn('/foo/app/foo/bar')
                ->shouldReceive('getPathFromExtensionName')->times(4)->with('/foo/path/laravel/framework')->andReturn('/foo/path/laravel/framework');



        $files->shouldReceive('isDirectory')->once()->with('/foo/path/foo/bar/resources/database/migrations/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/foo/bar/resources/database/seeds/')->andReturn(true)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/foo/bar/resources/seeds/')->andReturn(true)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/foo/bar/src/seeds/')->andReturn(true)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/foo/bar/resources/migrations/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/foo/bar/src/migrations/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/app/foo/bar/resources/database/migrations/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/app/foo/bar/resources/migrations/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/app/foo/bar/src/migrations/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/app/foo/bar/resources/database/seeds/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/app/foo/bar/resources/seeds/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/app/foo/bar/src/seeds/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/laravel/framework/resources/database/migrations/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/laravel/framework/resources/migrations/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/laravel/framework/src/migrations/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/laravel/framework/resources/database/seeds/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/laravel/framework/resources/seeds/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->with('/foo/path/laravel/framework/src/seeds/')->andReturn(false)
                ->shouldReceive('allFiles')->times(3)->andReturn([]);


        $repository->shouldReceive('createRepository')->never()->andReturn(null);

        $seeder = m::mock('\Illuminate\Database\Seeder');

        $stub = new MigrateManager($app, $migrator, $seeder);
        $stub->extension('foo/bar');
        $stub->extension('laravel/framework');
    }

    /**
     * Test Antares\Publisher\MigrateManager::foundation() method.
     *
     * @test
     */
    public function testFoundationMethod()
    {
        $app = $this->app;

        $app['files']     = $files            = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['migrator']  = $migrator         = m::mock('\Illuminate\Database\Migrations\Migrator');
        $app['path.base'] = '/foo/path/';

        $repository = m::mock('\Illuminate\Database\Migrations\DatabaseMigrationRepository');

        $files->shouldReceive('isDirectory')->once()->with('/foo/path/src/core/memory/resources/database/migrations/')->andReturn(false)
                ->shouldReceive('isDirectory')->once()->withAnyArgs()->andReturn(false)
                ->shouldReceive('allFiles')->times(2)->andReturn([]);

        $migrator->shouldReceive('getRepository')->once()->andReturn($repository)
                ->shouldReceive('run')->once()->with('/foo/path/src/core/auth/resources/database/migrations/')->andReturn(null)
                ->shouldReceive('run')->once()->with("/foo/path/src/core/src/components/auth/resources/database/migrations/")->andReturn(null)
                ->shouldReceive('getMigrationFiles')->once()->with("/foo/path/src/core/src/components/auth/resources/database/migrations/")->andReturn([]);
        $repository->shouldReceive('repositoryExists')->once()->andReturn(true)
                ->shouldReceive('createRepository')->never()->andReturn(null)
                ->shouldReceive('getRan')->once()->andReturn(null);

        $seeder = m::mock('\Illuminate\Database\Seeder');

        $stub = new MigrateManager($app, $migrator, $seeder);
        $stub->foundation();
    }

}

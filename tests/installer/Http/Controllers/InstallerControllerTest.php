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


namespace Antares\Installation\Http\Controllers\TestCase;

use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Support\Facades\Config;

class InstallerControllerTest extends TestCase
{

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app->make('Antares\Foundation\Bootstrap\LoadExpresso')->bootstrap($app);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Antares\Installation\InstallerServiceProvider',
        ];
    }

    /**
     * Test GET /admin/install.
     *
     * @test
     */
    public function testGetIndexAction()
    {
        $dbConfig = [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'database',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ];

        $requirement = m::mock('\Antares\Contracts\Installation\Requirement');
        $requirement->shouldReceive('check')->once()->andReturn(true)
                ->shouldReceive('getChecklist')->once()->andReturn([
            'databaseConnection' => [
                'is'       => true,
                'should'   => true,
                'explicit' => true,
                'data'     => [],
            ],
        ]);
        $user        = m::mock('UserEloquent', '\Antares\Model\User');
        $this->app->bind('UserEloquent', function () use ($user) {
            return $user;
        });
        $this->app->bind('Antares\Contracts\Installation\Requirement', function () use ($requirement) {
            return $requirement;
        });
        Config::set('database.default', 'mysql');
        Config::set('auth', ['driver' => 'eloquent', 'model' => 'UserEloquent']);
        Config::set('database.connections.mysql', $dbConfig);



        $this->call('GET', 'admin/install');
        $this->assertResponseOk();
        $this->assertViewHasAll([
            'database',
            'auth',
            'authentication',
            'installable',
            'checklist',
        ]);
    }

    /**
     * Test GET /admin/install when auth driver is not Eloquent.
     *
     * @test
     */
    public function testGetIndexActionWhenAuthDriverIsNotEloquent()
    {
        $dbConfig = [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'database',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ];

        $installer = m::mock('\Antares\Contracts\Installation\Installation');
        $installer->shouldReceive('bootInstallerFiles')->once()->andReturnNull();

        $this->app->bind('Antares\Contracts\Installation\Installation', function () use ($installer) {
            return $installer;
        });

        $requirement = m::mock('\Antares\Contracts\Installation\Requirement');
        $requirement->shouldReceive('check')->once()->andReturn(true)
                ->shouldReceive('getChecklist')->once()->andReturn([
            'databaseConnection' => [
                'is'       => true,
                'should'   => true,
                'explicit' => true,
                'data'     => [],
            ],
        ]);

        $this->app->bind('Antares\Contracts\Installation\Requirement', function () use ($requirement) {
            return $requirement;
        });

        Config::set('database.default', 'mysql');
        Config::set('auth', ['driver' => 'eloquent', 'model' => 'UserNotAvailableForAuthModel']);
        Config::set('database.connections.mysql', $dbConfig);

        $this->call('GET', 'admin/install');
        $this->assertResponseOk();
        $this->assertViewHasAll([
            'database',
            'auth',
            'authentication',
            'installable',
            'checklist',
        ]);
    }

    /**
     * Test GET /admin/install/prepare.
     *
     * @test
     */
    public function testGetPrepareAction()
    {
        $installer = m::mock('\Antares\Contracts\Installation\Installation');
        $installer->shouldReceive('bootInstallerFiles')->once()->andReturnNull()
                ->shouldReceive('migrate')->once()->andReturnNull();

        $this->app->bind('Antares\Contracts\Installation\Installation', function () use ($installer) {
            return $installer;
        });

        $this->call('GET', 'admin/install/prepare');
        $this->assertRedirectedTo(handles('antares::install/create'));
    }

    /**
     * Test GET /admin/install/create.
     *
     * @test
     */
    public function testGetCreateAction()
    {
        $this->call('GET', 'admin/install/create');
        $this->assertResponseOk();
        $this->assertViewHas('siteName', 'Antares Platform');
    }

    /**
     * Test GET /admin/install/create.
     *
     * @test
     */
    public function testPostCreateAction()
    {
        $input     = [];
        $installer = m::mock('\Antares\Contracts\Installation\Installation');
        $installer->shouldReceive('bootInstallerFiles')->once()->andReturnNull()
                ->shouldReceive('createAdmin')->once()->with($input)->andReturn(true);

        $this->app->bind('Antares\Contracts\Installation\Installation', function () use ($installer) {
            return $installer;
        });

        $this->call('POST', 'admin/install/create', $input);
        $this->assertRedirectedTo(handles('antares::install/done'));
    }

    /**
     * Test GET /admin/install/create when create admin failed.
     *
     * @test
     */
    public function testPostCreateActionWhenCreateAdminFailed()
    {
        $input     = [];
        $installer = m::mock('\Antares\Contracts\Installation\Installation');
        $installer->shouldReceive('bootInstallerFiles')->once()->andReturnNull()
                ->shouldReceive('createAdmin')->once()->with($input)->andReturn(false);

        $this->app->bind('Antares\Contracts\Installation\Installation', function () use ($installer) {
            return $installer;
        });

        $this->call('POST', 'admin/install/create', $input);
        $this->assertRedirectedTo(handles('antares::install/create'));
    }

    /**
     * Test GET /admin/install/done.
     *
     * @test
     */
    public function testGetDoneAction()
    {
        
    }

}

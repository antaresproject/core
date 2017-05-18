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

namespace Antares\Installation\Http\Controllers\TestCase;

use Antares\Testing\ApplicationTestCase;
use Illuminate\Support\Facades\Config;
use Mockery as m;

class InstallerControllerTest extends ApplicationTestCase
{

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
     * Test GET /antares/install.
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

        $user = m::mock('UserEloquent', '\Antares\Model\User');
        $this->app->bind('UserEloquent', function () use ($user) {
            return $user;
        });
        $this->app->bind('Antares\Contracts\Installation\Requirement', function () use ($requirement) {
            return $requirement;
        });
        Config::set('database.default', 'mysql');
        Config::set('auth', ['driver' => 'eloquent', 'model' => 'UserEloquent']);
        Config::set('database.connections.mysql', $dbConfig);



        $this->call('GET', '/antares/install');

        $this->assertResponseStatus(302);

        $this->assertRedirectedTo(handles('/'));
    }

    /**
     * Test GET /antares/install when auth driver is not Eloquent.
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


        $this->app->bind('Antares\Contracts\Installation\Requirement', function () use ($requirement) {
            return $requirement;
        });

        Config::set('database.default', 'mysql');
        Config::set('auth', ['driver' => 'eloquent', 'model' => 'UserNotAvailableForAuthModel']);
        Config::set('database.connections.mysql', $dbConfig);

        $this->call('GET', 'antares/install');
        $this->assertResponseStatus(302);
        $this->assertRedirectedTo(handles('/'));
    }

    /**
     * Test GET /antares/install/prepare.
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

        $this->call('GET', 'antares/install/prepare');
        $this->assertRedirectedTo(handles('antares::install/create'));
    }

    /**
     * Test GET /antares/install/create.
     *
     * @test
     */
    public function testGetCreateAction()
    {
        $this->call('GET', 'antares/install/create');
        $this->assertResponseStatus(302);
        $this->assertRedirectedTo(handles('/'));
    }

    /**
     * Test GET /antares/install/create.
     *
     * @test
     */
    public function testPostCreateAction()
    {
        $input     = [];
        $installer = m::mock('\Antares\Contracts\Installation\Installation');
        $installer->shouldReceive('bootInstallerFiles')->once()->andReturnNull();

        $this->app->bind('Antares\Contracts\Installation\Installation', function () use ($installer) {
            return $installer;
        });

        $this->call('POST', 'antares/install/create', $input);
        $this->assertResponseStatus(302);
        $this->assertRedirectedTo(handles('/'));
    }

    /**
     * Test GET /antares/install/done.
     *
     * @test
     */
    public function testGetDoneAction()
    {
        $this->call('GET', 'antares/install/done');
        $this->assertResponseStatus(302);
        $this->assertRedirectedTo(handles('antares::/install/completed'));
    }

}

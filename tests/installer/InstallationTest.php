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

namespace Antares\Installation\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Antares\Installation\Installation;
use Antares\Testing\ApplicationTestCase;
use Antares\Support\Traits\Testing\EloquentConnectionTrait;

class InstallationTest extends ApplicationTestCase
{

    use EloquentConnectionTrait;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        Facade::clearResolvedInstances();
    }

    /**
     * Get User input.
     *
     * @access private
     *
     * @return array
     */
    private function getUserInput()
    {
        return [
            'site_name' => 'Antares Platform',
            'email'     => 'admin@antaresplatform.com',
            'password'  => '123456',
            'firstname' => 'Antares',
            'lastname'  => 'Project',
        ];
    }

    /**
     * Get validation rules.
     *
     * @access private
     *
     * @return array
     */
    private function getValidationRules()
    {
        return [
            'email'     => ['required', 'email'],
            'password'  => ['required'],
            'fullname'  => ['required'],
            'site_name' => ['required'],
        ];
    }

    /**
     * Test Antares\Foundation\Installation\Installation::bootInstallerFiles() method.
     *
     * @test
     */
    public function testBootInstallerFilesMethod()
    {
        $app                        = $this->app;
        $this->app['path']          = '/var/laravel/app';
        $this->app['path.database'] = '/var/laravel/database';
        $app['files']               = $files                      = m::mock('\Illuminate\Filesystem\Filesystem');

        $files->shouldReceive('exists')->once()->with('/var/laravel/database/antares/installer.php')->andReturn(true)
                ->shouldReceive('requireOnce')->once()->with('/var/laravel/database/antares/installer.php')->andReturnNull()
                ->shouldReceive('exists')->once()->with('/var/laravel/app/antares/installer.php')->andReturn(true)
                ->shouldReceive('requireOnce')->once()->with('/var/laravel/app/antares/installer.php')->andReturnNull();

        $stub = new Installation($app);
        $this->assertNull($stub->bootInstallerFiles());
    }

    /**
     * Test Antares\Foundation\Installation\Installation::migrate() method.
     *
     * @test
     */
    public function testMigrateMethod()
    {
        $stub = new Installation($this->app);
        $this->assertTrue($stub->migrate());
    }

    /**
     * Test Antares\Foundation\Installation\Installation::createAdmin() method.
     *
     * @test
     */
    public function testCreateAdminMethod()
    {
        $input = $this->getUserInput();
        $stub  = new Installation($this->app);
        $this->assertFalse($stub->createAdmin($input, false));
    }

    /**
     * Test Antares\Foundation\Installation\Installation::createAdmin() method
     * with validation errors.
     *
     * @test
     */
    public function testCreateAdminMethodWithValidationErrors()
    {
        $app              = $this->app;
        $app['files']     = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['validator'] = $validator        = m::mock('\Illuminate\Contracts\Validation\Validator');
        $app['session']   = $session          = m::mock('\Illuminate\Session\SessionInterface');

        $session->shouldReceive('flash')->once()->with('errors', m::type('Object'))->andReturnNull();

        $stub = new Installation($app);
        $this->assertFalse($stub->createAdmin([]));
    }

    /**
     * Test Antares\Foundation\Installation\Installation::createAdmin() method
     * throws exception.
     *
     * @test
     */
    public function testCreateAdminMethodThrowsException()
    {
        $app                     = $this->app;
        $app['files']            = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['validator']        = $validator               = m::mock('\Illuminate\Contratcs\Validation\Validator');
        $app['antares.user']     = $user                    = m::mock('\Antares\Model\User');
        $app['antares.messages'] = $messages                = m::mock('\Antares\Contracts\Messages\MessageBag');

        $input = $this->getUserInput();

        $messages->shouldReceive('add')->once()->with('error', m::any())->andReturnNull();

        $stub = new Installation($this->app);
        $this->assertFalse($stub->createAdmin($input, false));
    }

}

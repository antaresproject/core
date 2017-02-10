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

class InstallationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var Illuminate\Foundation\Application
     */
    protected $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app = new Container();
        $this->app['translator'] = $translator = m::mock('\Illuminate\Translation\Translator');

        $translator->shouldReceive('trans')->andReturn('foo');

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($this->app);
        Container::setInstance($this->app);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);
        m::close();
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
            'fullname'  => 'Administrator',
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
        $app = $this->app;
        $this->app['path'] = '/var/laravel/app';
        $this->app['path.database'] = '/var/laravel/database';
        $app['files'] = $files = m::mock('\Illuminate\Filesystem\Filesystem');

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
        $app = $this->app;
        $app['files'] = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['antares.publisher.migrate'] = $migrate = m::mock('\Antares\Extension\Publisher\MigrateManager')->makePartial();
        $app['events'] = $events = m::mock('\Illuminate\Contracts\Events\Dispatcher');

        $migrate->shouldReceive('foundation')->once()->andReturnNull();
        $events->shouldReceive('fire')->once()->with('antares.install.schema')->andReturnNull();

        $stub = new Installation($app);
        $this->assertTrue($stub->migrate());
    }
    /**
     * Test Antares\Foundation\Installation\Installation::createAdmin() method.
     *
     * @test
     */
    public function testCreateAdminMethod()
    {
        $app = $this->app;
        $app['files'] = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['validator'] = $validator = m::mock('\Illuminate\Contracts\Validation\Validator');
        $app['antares.role'] = $role = m::mock('\Antares\Model\Role');
        $app['antares.user'] = $user = m::mock('\Antares\Model\User');
        $app['antares.messages'] = $messages = m::mock('\Antares\Contracts\Messages\MessageBag');
        $app['events'] = $events = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['antares.memory'] = $memory = m::mock('\Antares\Memory\MemoryManager[make]', [$this->app]);
        $app['config'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['antares.acl'] = $acl = m::mock('\Antares\Contracts\Authorization\Authorization');

        $memoryProvider = m::mock('\Antares\Contracts\Memory\Provider');
        $aclFluent = m::mock('\Antares\Auth\Acl\Fluent');
        $aclFluent->shouldReceive('attach')->twice()->andReturnNull();

        $input = $this->getUserInput();
        $rules = $this->getValidationRules();

        $validator->shouldReceive('make')->once()->with($input, $rules)->andReturn($validator)
            ->shouldReceive('fails')->once()->andReturn(false);
        $user->shouldReceive('newQuery')->once()->andReturn($user)
            ->shouldReceive('all')->once()->andReturnNull()
            ->shouldReceive('newInstance')->once()->andReturn($user)
            ->shouldReceive('fill')->once()->andReturnNull()
            ->shouldReceive('save')->once()->andReturnNull()
            ->shouldReceive('roles')->once()->andReturn($user)
            ->shouldReceive('sync')->once()->with([1])->andReturnNull();
        $role->shouldReceive('newQuery')->once()->andReturn($role)
            ->shouldReceive('lists')->once()->with('name', 'id')->andReturn(['admin', 'member']);
        $events->shouldReceive('fire')->once()->with('antares.install: user', [$user, $input])->andReturnNull()
            ->shouldReceive('fire')->once()->with('antares.install: acl', [$acl])->andReturnNull();
        $memory->shouldReceive('make')->once()->andReturn($memoryProvider);
        $memoryProvider->shouldReceive('put')->once()->with('site.name', $input['site_name'])->andReturnNull()
            ->shouldReceive('put')->once()->with('site.theme', ['frontend' => 'default', 'backend' => 'default'])
                ->andReturnNull()
            ->shouldReceive('put')->once()->with('email', 'email-config')->andReturnNull()
            ->shouldReceive('put')->once()->with('email.from', ['name' => $input['site_name'], 'address' => $input['email']])
                ->andReturnNull();
        $config->shouldReceive('get')->once()->with('antares/foundation::roles.admin', 1)->andReturn(1)
            ->shouldReceive('get')->once()->with('mail')->andReturn('email-config');
        $acl->shouldReceive('make')->once()->with('antares')->andReturn($acl)
            ->shouldReceive('actions')->once()->andReturn($aclFluent)
            ->shouldReceive('roles')->once()->andReturn($aclFluent)
            ->shouldReceive('allow')->once()->andReturnNull()
            ->shouldReceive('attach')->once()->with($memoryProvider)->andReturnNull();

        $messages->shouldReceive('add')->once()->with('success', m::any())->andReturnNull();

        $stub = new Installation($app);
        $this->assertTrue($stub->createAdmin($input, false));
    }

    /**
     * Test Antares\Foundation\Installation\Installation::createAdmin() method
     * with validation errors.
     *
     * @test
     */
    public function testCreateAdminMethodWithValidationErrors()
    {
        $app = $this->app;
        $app['files'] = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['validator'] = $validator = m::mock('\Illuminate\Contracts\Validation\Validator');
        $app['session'] = $session = m::mock('\Illuminate\Session\SessionInterface');

        $input = $this->getUserInput();
        $rules = $this->getValidationRules();

        $validator->shouldReceive('make')->once()->with($input, $rules)->andReturn($validator)
            ->shouldReceive('fails')->once()->andReturn(true)
            ->shouldReceive('messages')->once()->andReturn('foo-errors');
        $session->shouldReceive('flash')->once()->with('errors', 'foo-errors')->andReturnNull();

        $stub = new Installation($app);
        $this->assertFalse($stub->createAdmin($input));
    }

    /**
     * Test Antares\Foundation\Installation\Installation::createAdmin() method
     * throws exception.
     *
     * @test
     */
    public function testCreateAdminMethodThrowsException()
    {
        $app = $this->app;
        $app['files'] = m::mock('\Illuminate\Filesystem\Filesystem');
        $app['validator'] = $validator = m::mock('\Illuminate\Contratcs\Validation\Validator');
        $app['antares.user'] = $user = m::mock('\Antares\Model\User');
        $app['antares.messages'] = $messages = m::mock('\Antares\Contracts\Messages\MessageBag');

        $input = $this->getUserInput();
        $rules = $this->getValidationRules();

        $validator->shouldReceive('make')->once()->with($input, $rules)->andReturn($validator)
            ->shouldReceive('fails')->once()->andReturn(false);
        $user->shouldReceive('newQuery')->once()->andReturn($user)
            ->shouldReceive('all')->once()->andReturn(['not so empty']);
        $messages->shouldReceive('add')->once()->with('error', m::any())->andReturnNull();

        $stub = new Installation($app);
        $this->assertFalse($stub->createAdmin($input, false));
    }
}

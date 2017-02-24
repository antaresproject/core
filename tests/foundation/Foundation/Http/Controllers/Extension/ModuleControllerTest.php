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

namespace Antares\Foundation\Http\Controllers\Extension\TestCase;

use Antares\Foundation\Http\Presenters\Module as ModulePresenter;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Testing\ApplicationTestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Antares\Support\Facades\Messages;
use Antares\Support\Facades\Extension;
use Antares\Support\Facades\Publisher;
use Antares\Support\Collection;
use Mockery as m;

class ModuleControllerTest extends ApplicationTestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->disableMiddlewareForAllTests();

        Extension::shouldReceive('started')->once()->with('laravel/framework')->andReturn(false)
                ->shouldReceive('permission')->once()->with('laravel/framework')->andReturn(true)
                ->shouldReceive('activate')->once()->with('laravel/framework')->andReturn(true)
                ->shouldReceive('activated')->once()->with('laravel/framework')->andReturn(true)
                ->shouldReceive('deactivate')->once()->with('laravel/framework')->andReturn(true)
                ->shouldReceive('deactivated')->once()->with('laravel/framework')->andReturn(true)
                ->shouldReceive('detect')->once()->andReturn('foo')
                ->shouldReceive('finish')->once()->withNoArgs()->andReturn(true)
                ->shouldReceive('attach')->once()->with(m::type('Object'))->andReturnSelf()
                ->shouldReceive('boot')->once()->withNoArgs()->andReturnSelf();
    }

    /**
     * Bind dependencies.
     *
     * @return array
     */
    protected function bindDependencies()
    {
        $presenter = m::mock('\Antares\Foundation\Http\Presenters\Extension');
        $validator = m::mock('\Antares\Foundation\Validation\Extension');

        App::instance('Antares\Foundation\Http\Presenters\Extension', $presenter);
        App::instance('Antares\Foundation\Validation\Extension', $validator);

        return [$presenter, $validator];
    }

    /**
     * Test GET /antares/modules.
     *
     * @test
     */
    public function testGetIndexAction()
    {
        $presenter = m::mock(ModulePresenter::class);
        $presenter->shouldReceive('setCategory')->once()->andReturnSelf()
                ->shouldReceive('modules')->once()->withNoArgs()->andReturn(new Collection([
            [
                'full_name'   => 'Foo Component',
                'description' => 'Foo Component',
                'author'      => 'Foo',
                'version'     => '0.9.0',
                'url'         => 'http://foo.com',
                'name'        => 'foo',
                'activated'   => 1,
                'started'     => 0,
                'category'    => 'foo'
            ]
        ]));
        App::instance(ModulePresenter::class, $presenter);

        View::shouldReceive('make')->once()->withAnyArgs()->andReturnSelf()
                ->shouldReceive('addNamespace')->once()->withAnyArgs()->andReturnSelf()
                ->shouldReceive('share')->once()->with(m::type('string'), m::type('string'))->andReturnSelf()
                ->shouldReceive('render')->once()->withNoArgs()->andReturn('foo');

        $this->call('GET', 'antares/modules');
        $this->assertResponseOk();
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/activate.
     *
     * @test
     */
    public function testGetActivateAction()
    {

        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        $this->call('GET', 'antares/modules/products/laravel/framework/activate');
        $this->assertRedirectedTo('antares/modules/laravel');
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/activate when module is already
     * started.
     *
     * @test
     */
    public function testGetActivateActionGivenStartedModule()
    {


        $this->call('GET', 'antares/modules/products/laravel/framework/activate');
        $this->assertRedirectedTo('antares/modules/laravel');
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/activate with migration error.
     *
     * @test
     */
    public function testGetActivateActionGivenMigrationError()
    {
        Publisher::shouldReceive('queue')->once()->with('laravel/framework')->andReturnNull();
        $this->call('GET', 'antares/modules/products/laravel/framework/activate');
        $this->assertRedirectedTo('antares/modules/laravel');
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/deactivate.
     *
     * @test
     */
    public function testGetDeactivateAction()
    {
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        $this->call('GET', 'antares/modules/products/laravel/framework/deactivate');
        $this->assertRedirectedTo('antares/modules/laravel');
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/deactivate when module is not
     * started.
     *
     * @test
     */
    public function testGetDeactivateActionGivenNotStartedModule()
    {
        $this->call('GET', 'antares/modules/products/laravel/framework/deactivate');
        $this->assertRedirectedTo('antares/modules/laravel');
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/uninstall.
     *
     * @test
     */
    public function testGetUninstallAction()
    {
        Extension::shouldReceive('uninstall')->once()->with('laravel/framework')->andReturn(true);
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();

        $uninstaller = m::mock('\Antares\Extension\Processor\Uninstaller');
        $uninstaller->shouldReceive('uninstall')->andReturn(true);
        App::instance('Antares\Extension\Processor\Uninstaller', $uninstaller);
        $this->call('GET', 'antares/modules/products/laravel/framework/uninstall');
        $this->assertRedirectedTo('antares/modules/laravel');
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/uninstall with migration error.
     *
     * @test
     */
    public function testGetUninstallActionWithError()
    {
        $uninstaller = m::mock('\Antares\Extension\Processor\Uninstaller');
        $uninstaller->shouldReceive('uninstall')->andReturn(false);
        App::instance('Antares\Extension\Processor\Uninstaller', $uninstaller);
        $this->call('GET', 'antares/modules/products/laravel/framework/uninstall');
        $this->assertRedirectedTo('antares/modules/laravel');
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/uninstall.
     *
     * @test
     */
    public function testGetDeleteAction()
    {
        Extension::shouldReceive('delete')->once()->with('laravel/framework')->andReturn(true);

        $delete = m::mock('\Antares\Extension\Processor\Delete');
        $delete->shouldReceive('delete')->with(
                m::type('Antares\Foundation\Http\Controllers\Extension\ModuleController'), m::type('Antares\Extension\Processor\Uninstaller'), m::type('Illuminate\Support\Fluent')
        )->andReturn();

        App::instance('Antares\Extension\Processor\Delete', $delete);
        $response = $this->call('GET', 'antares/modules/products/laravel/framework/delete');
        $this->assertResponseOk();
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/update.
     *
     * @test
     */
    public function testGetUpdateAction()
    {
        $response = $this->call('GET', 'antares/modules/addons/laravel/framework/update');
        $this->assertResponseStatus(404);
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/update when module is not
     * started.
     *
     * @test
     */
    public function testGetUpdateActionGivenNotStartedExtension()
    {
        Extension::shouldReceive('started')->once()->with('foo')->andReturn(false);
        $response = $this->call('GET', 'antares/modules/addons/foo/update');
        $this->assertResponseStatus(404);
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * Test GET /antares/modules/(:category)/(:name)/update with migration error.
     *
     * @test
     */
    public function testGetUpdateActionGivenMgrationError()
    {
        Extension::shouldReceive('started')->once()->with('foo')->andReturn(true);
        Extension::shouldReceive('permission')->once()->with('foo')->andReturn(false);
        Publisher::shouldReceive('queue')->once()->with('foo')->andReturnNull();
        $this->call('GET', 'antares/modules/addons/foo/update');
        $this->assertRedirectedTo('antares/publisher');
    }

}

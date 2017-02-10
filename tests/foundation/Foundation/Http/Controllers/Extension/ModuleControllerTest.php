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

use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Antares\Support\Facades\Messages;
use Antares\Support\Facades\Extension;
use Antares\Support\Facades\Publisher;
use Antares\Support\Facades\Foundation;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ModuleControllerTest extends TestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->disableMiddlewareForAllTests();
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
     * Test GET /admin/modules.
     *
     * @test
     */
    public function testGetIndexAction()
    {
        Extension::shouldReceive('detect')->once()->andReturn('foo');
        View::shouldReceive('make')->once()
                ->withAnyArgs()
                ->andReturn('foo');
        View::shouldReceive('addNamespace')->once()
                ->withAnyArgs()
                ->andReturn('foo');

        $this->call('GET', 'admin/modules');
        $this->assertResponseOk();
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/activate.
     *
     * @test
     */
    public function testGetActivateAction()
    {
        Extension::shouldReceive('started')->once()->with('laravel/framework')->andReturn(false);
        Extension::shouldReceive('permission')->once()->with('laravel/framework')->andReturn(true);
        Extension::shouldReceive('activate')->once()->with('laravel/framework')->andReturn(true);
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        Foundation::shouldReceive('handles')->once()->with('antares::modules/laravel', [])->andReturn('modules');

        $this->call('GET', 'admin/modules/products/laravel/framework/activate');
        $this->assertRedirectedTo('modules');
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/activate when module is already
     * started.
     *
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetActivateActionGivenStartedModule()
    {
        Extension::shouldReceive('started')->once()->with('laravel/framework')->andReturn(true);

        $this->call('GET', 'admin/modules/products/laravel/framework/activate');
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/activate with migration error.
     *
     * @test
     */
    public function testGetActivateActionGivenMigrationError()
    {
        Extension::shouldReceive('started')->once()->with('laravel/framework')->andReturn(false);
        Extension::shouldReceive('permission')->once()->with('laravel/framework')->andReturn(false);
        Publisher::shouldReceive('queue')->once()->with('laravel/framework')->andReturnNull();
        Foundation::shouldReceive('handles')->once()->with('antares::publisher', [])->andReturn('publisher');

        $this->call('GET', 'admin/modules/products/laravel/framework/activate');
        $this->assertRedirectedTo('publisher');
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/deactivate.
     *
     * @test
     */
    public function testGetDeactivateAction()
    {
        Extension::shouldReceive('started')->once()->with('laravel/framework')->andReturn(true);
        Extension::shouldReceive('deactivate')->once()->with('laravel/framework')->andReturn(true);
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        Foundation::shouldReceive('handles')->once()->with('antares::modules/laravel', [])->andReturn('modules');

        $this->call('GET', 'admin/modules/products/laravel/framework/deactivate');
        $this->assertRedirectedTo('modules');
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/deactivate when module is not
     * started.
     *
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetDeactivateActionGivenNotStartedModule()
    {
        Extension::shouldReceive('started')->once()->with('laravel/framework')->andReturn(false);
        Extension::shouldReceive('activated')->once()->with('laravel/framework')->andReturn(false);

        $this->call('GET', 'admin/modules/products/laravel/framework/deactivate');
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/uninstall.
     *
     * @test
     */
    public function testGetUninstallAction()
    {
        Extension::shouldReceive('started')->once()->with('laravel/framework')->andReturn(true);

        Extension::shouldReceive('permission')->once()->with('laravel/framework')->andReturn(true);
        Extension::shouldReceive('uninstall')->once()->with('laravel/framework')->andReturn(true);
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        Foundation::shouldReceive('handles')->once()->with('antares::modules/laravel', [])->andReturn('modules');

        $uninstaller = m::mock('\Antares\Extension\Processor\Uninstaller');
        $uninstaller->shouldReceive('uninstall')->andReturn(true);
        App::instance('Antares\Extension\Processor\Uninstaller', $uninstaller);
        $this->call('GET', 'admin/modules/products/laravel/framework/uninstall');
        $this->assertRedirectedTo('modules');
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/uninstall with migration error.
     *
     * @test
     */
    public function testGetUninstallActionWithError()
    {
        Extension::shouldReceive('started')->once()->with('laravel/framework')->andReturn(false);
        Extension::shouldReceive('permission')->once()->with('laravel/framework')->andReturn(false);
        Publisher::shouldReceive('queue')->once()->with('laravel/framework')->andReturnNull();
        Foundation::shouldReceive('handles')->once()->with("antares::modules/laravel", [])->andReturn('publisher');

        $uninstaller = m::mock('\Antares\Extension\Processor\Uninstaller');
        $uninstaller->shouldReceive('uninstall')->andReturn(false);
        App::instance('Antares\Extension\Processor\Uninstaller', $uninstaller);
        $this->call('GET', 'admin/modules/products/laravel/framework/uninstall');
        $this->assertRedirectedTo('publisher');
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/uninstall.
     *
     * @test
     */
    public function testGetDeleteAction()
    {
        Extension::shouldReceive('started')->once()->with('laravel/framework')->andReturn(true);

        Extension::shouldReceive('permission')->once()->with('laravel/framework')->andReturn(true);
        Extension::shouldReceive('delete')->once()->with('laravel/framework')->andReturn(true);
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        Foundation::shouldReceive('handles')->once()->with('antares::modules/laravel', [])->andReturn('modules');

        $delete = m::mock('\Antares\Extension\Processor\Delete');
        $delete->shouldReceive('delete')->with(
                m::type('Antares\Foundation\Http\Controllers\Extension\ModuleController'), m::type('Antares\Extension\Processor\Uninstaller'), m::type('Illuminate\Support\Fluent')
        )->andReturn();

        App::instance('Antares\Extension\Processor\Delete', $delete);

        $this->assertInstanceOf('Illuminate\Http\Response', $this->call('GET', 'admin/modules/products/laravel/framework/delete'));
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/update.
     *
     * @test
     */
    public function testGetUpdateAction()
    {
        Extension::shouldReceive('started')->once()->with('addons/foo')->andReturn(true);
        Extension::shouldReceive('permission')->once()->with('addons/foo')->andReturn(true);
        Extension::shouldReceive('publish')->once()->with('addons/foo')->andReturn(true);
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();
        Foundation::shouldReceive('handles')->once()->with('antares::modules/addons', [])->andReturn('modules');

        $this->call('GET', 'admin/modules/addons/foo/update');
        $this->assertRedirectedTo('modules');
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/update when module is not
     * started.
     *
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetUpdateActionGivenNotStartedExtension()
    {
        Extension::shouldReceive('started')->once()->with('addons/foo')->andReturn(false);

        $this->call('GET', 'admin/modules/addons/foo/update');
    }

    /**
     * Test GET /admin/modules/(:category)/(:name)/update with migration error.
     *
     * @test
     */
    public function testGetUpdateActionGivenMgrationError()
    {
        Extension::shouldReceive('started')->once()->with('addons/foo')->andReturn(true);
        Extension::shouldReceive('permission')->once()->with('addons/foo')->andReturn(false);
        Publisher::shouldReceive('queue')->once()->with('addons/foo')->andReturnNull();
        Foundation::shouldReceive('handles')->once()->with('antares::publisher', [])->andReturn('publisher');

        $this->call('GET', 'admin/modules/addons/foo/update');
        $this->assertRedirectedTo('publisher');
    }

}

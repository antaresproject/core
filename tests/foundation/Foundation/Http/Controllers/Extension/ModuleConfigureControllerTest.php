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

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Antares\Support\Facades\Messages;
use Antares\Support\Facades\Extension;
use Antares\Support\Facades\Foundation;
use Mockery as m;

class ModuleConfigureControllerTest extends TestCase
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
     * Test POST /admin/modules/(:category)/(:name)/configure.
     *
     * @test
     */
    public function testPostConfigureAction()
    {


        $input = [
            'handles' => 'foo',
            '_token'  => 'somesessiontoken',
        ];

        $memory = m::mock('\Antares\Contracts\Memory\Provider');
        list(, $validator) = $this->bindDependencies();

        $memory->shouldReceive('get')->once()
                ->with('extension.active.laravel/framework.config', [])->andReturn([])
                ->shouldReceive('get')->once()
                ->with('extension_laravel/framework', [])->andReturn([])
                ->shouldReceive('put')->once()
                ->with('extensions.active.laravel/framework.config', ['handles' => 'foo'])->andReturnNull()
                ->shouldReceive('put')->once()
                ->with('extension_laravel/framework', ['handles' => 'foo'])->andReturnNull();


        $validator->shouldReceive('with')->once()
                ->with($input, ["antares.validate: extension.laravel/framework"])->andReturn($validator)
                ->shouldReceive('fails')->once()->andReturn(false);

        Extension::shouldReceive('started')->once()->with('laravel/framework')->andReturn(true)
                ->shouldReceive('finish')->once()->withNoArgs()->andReturn(true);
        Foundation::shouldReceive('memory')->once()->andReturn($memory);
        Foundation::shouldReceive('handles')->once()->with('antares::modules/laravel', [])->andReturn('modules');
        Messages::shouldReceive('add')->once()->with('success', m::any())->andReturnNull();

        $this->call('POST', 'antares/modules/addons/laravel/framework/configure', $input);

        $this->assertRedirectedTo('modules');
    }

    /**
     * Test POST /admin/modules/(:category)/(:name)/configure when module is not
     * started.
     *
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testPostConfigureActionGivenNotStartedModule()
    {

        $input = [
            'handles' => 'foo',
            '_token'  => 'somesessiontoken',
        ];

        Extension::shouldReceive('started')->once()->with('foo')->andReturn(false);
        $this->call('POST', 'antares/modules/addons/foo', $input);
    }

    /**
     * Test POST /admin/modules/(:category)/(:name)/configure with validation error.
     *
     * @test
     */
    public function testPostConfigureActionGivenValidationError()
    {
        $input  = [
            'handles' => 'foo',
            '_token'  => 'somesessiontoken',
        ];
        $memory = m::mock('\Antares\Contracts\Memory\Provider');
        $memory->shouldReceive('get')->once()->with("extension.active.foo.config", [])->andReturn([])
                ->shouldReceive('get')->once()->with("extension_foo", [])->andReturn([])
                ->shouldReceive('put')->once()->with("extension_foo", ['handles' => 'foo'])->andReturn([])
                ->shouldReceive('put')->once()->with('extensions.active.foo.config', ['handles' => 'foo'])->andReturnNull();


        Foundation::shouldReceive('memory')->once()->andReturn($memory);

        Extension::shouldReceive('started')->once()->with('foo')->andReturn(true)
                ->shouldReceive('option')->once()->andReturn(null)
                ->shouldReceive('handles')->once()->with('antares::modules/foo', [])->andReturn('modules');
        try {
            $this->call('POST', 'admin/modules/addons/foo/configure', $input);
            $this->assertRedirectedTo('modules');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $ex) {
            $this->markTestIncomplete('Component configuration is not yet implemented.');
        }
    }

}

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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Customfields\TestCase;

use Antares\Customfields\CustomFieldsServiceProvider;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Container\Container;
use Antares\Testing\TestCase;
use Mockery as m;

class CustomFieldsServiceProviderTest extends TestCase
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
     * Test Antares\Customfields\CustomFieldsServiceProvider::register() method.
     * @test
     */
    public function testRegisterMethod()
    {

        $app           = $this->app;
        $app['config'] = $config        = m::mock('\Illuminate\Contracts\Config\Repository');
        $app['events'] = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['files']  = m::mock('\Illuminate\Filesystem\Filesystem');
        $config->shouldReceive('get')->andReturn([]);


        $stub = new CustomFieldsServiceProvider($app);
        $stub->register();
        $this->assertInstanceOf('\Antares\Customfields\Model\Field', $app['antares.customfields.model']);
        $this->assertInstanceOf('\Antares\Customfields\Model\FieldView', $app['antares.customfields.model.view']);
        $this->assertInstanceOf('\Antares\Customfields\Model\FieldGroup', $app['antares.customfields.model.group']);
        $this->assertInstanceOf('\Antares\Customfields\Model\FieldCategory', $app['antares.customfields.model.category']);
        $this->assertInstanceOf('\Antares\Customfields\Model\FieldType', $app['antares.customfields.model.type']);
        $this->assertInstanceOf('\Antares\Customfields\Model\FieldTypeOption', $app['antares.customfields.model.type.option']);
        $this->assertInstanceOf('\Antares\Customfields\Model\FieldValidator', $app['antares.customfields.model.validator']);
        $this->assertInstanceOf('\Antares\Customfields\Model\FieldValidatorConfig', $app['antares.customfields.model.validator.config']);
    }

    /**
     * Test CustomFieldsServiceProvider::provides() method.    
     * @test
     */
    public function testProvidesMethod()
    {
        $app  = new Container();
        $stub = new CustomFieldsServiceProvider($app);
        $this->assertEquals([], $stub->provides());
    }

    /**
     * Test CustomfieldsServiceProvider is deferred.
     * @test
     */
    public function testServiceIsDeferred()
    {
        $app  = new Container();
        $stub = new CustomFieldsServiceProvider($app);
        $this->assertFalse($stub->isDeferred());
    }

    /**
     * test booting method
     */
    public function testExceptionThrowsWhenBoot()
    {
        $app        = m::mock(\Illuminate\Contracts\Foundation\Application::class);
        $app->shouldReceive('make')->with(\Illuminate\Routing\Router::class)->andReturn($router     = m::mock(\Illuminate\Routing\Router::class));
        $app->shouldReceive('make')->with(\Illuminate\Contracts\Events\Dispatcher::class)->andReturn($dispatcher = m::mock(\Illuminate\Contracts\Events\Dispatcher::class))
                ->shouldReceive('make')->with(\Illuminate\Contracts\Http\Kernel::class)->andReturn($kernel     = m::mock(\Illuminate\Contracts\Http\Kernel::class))
                ->shouldReceive('make')->with('config')->andReturn($config     = m::mock(\Illuminate\Contracts\Config\Repository::class))
                ->shouldReceive('make')->with('files')->andReturn($files      = m::mock(\Illuminate\Contracts\Filesystem\Filesystem::class))
                ->shouldReceive('make')->with('translator')->andReturn($translator = m::mock(\Antares\Translations\Translator::class))
                ->shouldReceive('make')->with('view')->andReturn($view       = m::mock(\Illuminate\Contracts\View\View::class))
                ->shouldReceive('make')->with('antares.acl')->andReturnSelf()
                ->shouldReceive('make')->with('antares/customfields')->andReturnSelf()
                ->shouldReceive('make')->with('antares.platform.memory')->andReturnSelf()
                ->shouldReceive('make')->with('antares.request')->andReturn($request    = m::mock(\Illuminate\Http\Request::class))
                ->shouldReceive('attach')->with(m::type('Object'))->andReturnSelf()
                ->shouldReceive('routesAreCached')->withNoArgs()->andReturn(true);

        $request->shouldReceive('shouldMakeApiResponse')->andReturn(false);
        $translator->shouldReceive('addNamespace')->with(m::type('String'), m::type('String'))->andReturnSelf();
        $dispatcher->shouldReceive('listen')->with(m::type('String'), m::type('String'))->andReturnSelf();

        $view->shouldReceive('addNamespace')->with(m::type('String'), m::type('String'))->andReturnSelf()
                ->shouldReceive('composer')->with(m::type('array'), m::type('String'))->andReturnSelf();
        $router->shouldReceive('aliasMiddleware')->with(m::type('String'), m::type('String'));


        $this->app['antares.customfields.model.category'] = $fieldCategory                                    = $this->app->make(\Antares\Customfields\Model\FieldCategory::class);

        $config->shouldReceive('get')->andReturn([]);
        $stub = new CustomFieldsServiceProvider($app);
        $this->assertNull($stub->boot());
    }

}

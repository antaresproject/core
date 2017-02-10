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




namespace Antares\Foundation\Http\Handlers\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Foundation\Http\Handlers\ModulesMenuHandler;

class ModulesMenuHandlerTest extends \TestCase
{

    /**
     * Test Antares\Foundation\Http\Handlers\ModulesMenuHandler::handle()
     * method with authorized user.
     *
     * @test
     */
    public function testCreatingMenuWithAuthorizedUser()
    {
        $app                          = new Container();
        $app['antares.extension']     = $extension                    = m::mock('\Antares\Contracts\Extension\Factory');
        $app['antares.app']           = $foundation                   = m::mock('\Antares\Contracts\Foundation\Foundation');
        $app['antares.platform.menu'] = $menu                         = m::mock('\Antares\Widget\Handlers\Menu');
        $app['translator']            = $translator                   = m::mock('\Illuminate\Translator\Translator');
        $translator->shouldReceive('trans')->with("antares/foundation::title.modules.products")
                ->andReturn('foo')
                ->shouldReceive('trans')->with("antares/foundation::title.modules.domains")
                ->andReturn('foo')
                ->shouldReceive('trans')->with("antares/foundation::title.modules.fraud")
                ->andReturn('foo')
                ->shouldReceive('trans')->with("antares/foundation::title.modules.addons")
                ->andReturn('foo');


        $provider      = m::mock('\Antares\Memory\Provider');
        $provider->shouldReceive('get')
                ->with(m::type('String'))
                ->andReturn([
                    'addons/foo' => [
                        'name'        => 'foo',
                        'full_name'   => 'Foo Sample Module',
                        'description' => 'Sample foo module BillEvo Platform',
                        'author'      => 'Lukasz Cirut',
                    ]
        ]);
        $memoryManager = m::mock('\Antares\Memory\MemoryManager');
        $memoryManager->shouldReceive('make')
                ->with('component.default')
                ->andReturn($provider);


        $app['antares.memory'] = $memoryManager;

        $app['Antares\Contracts\Authorization\Authorization'] = $acl                                                  = m::mock('\Antares\Contracts\Authorization\Authorization');

        $acl->shouldReceive('can')->with('manage-antares')->once()->andReturn(true);

        $translator->shouldReceive('trans')->once()->with('antares/foundation::title.modules.list')->andReturn('modules');
        $foundation->shouldReceive('handles')->once()->with('antares::modules')->andReturn('admin/modules');

        $menu->shouldReceive('add')->once()->andReturnSelf()
                ->shouldReceive('title')->once()->with('modules')->andReturnSelf()
                ->shouldReceive('html')->with(m::type('String'))->andReturnSelf()
                ->shouldReceive('link')->once()->with(m::type('String'))->andReturnSelf();

        $stub = new ModulesMenuHandler($app);
        $this->assertNull($stub->handle());
    }

    /**
     * Test Antares\Foundation\Http\Handlers\ModulesMenuHandler::handle()
     * method without authorized user.
     *
     * @test
     */
    public function testCreatingMenuWithoutAuthorizedUser()
    {
        $app                                                  = new Container();
        $app['antares.extension']                             = $extension                                            = m::mock('\Antares\Contracts\Extension\Factory');
        $app['antares.platform.menu']                         = $menu                                                 = m::mock('\Antares\Widget\Handlers\Menu');
        $app['Antares\Contracts\Authorization\Authorization'] = $acl                                                  = m::mock('\Antares\Contracts\Authorization\Authorization');

        $acl->shouldReceive('can')->with('manage-antares')->once()->andReturn(false);

        $stub = new ModulesMenuHandler($app);
        $this->assertNull($stub->handle());
    }

    /**
     * Test Antares\Foundation\Http\Handlers\ModulesMenuHandler::handle()
     * method without `antares.extension` bound to container.
     *
     * @test
     */
    public function testCreatingMenuWithoutBoundDependencies()
    {
        $app                                                  = new Container();
        $app['antares.platform.menu']                         = $menu                                                 = m::mock('\Antares\Widget\Handlers\Menu');
        $app['Antares\Contracts\Authorization\Authorization'] = $acl                                                  = m::mock('\Antares\Contracts\Authorization\Authorization');

        $stub = new ModulesMenuHandler($app);
        $this->assertNull($stub->handle());
    }

}

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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Http\Presenters\Tests;

use Antares\Tester\Http\Presenters\CollectivePresenter as Stub;
use Antares\Tester\Http\Presenters\CollectivePresenter;
use Antares\Tester\Http\Breadcrumb\Breadcrumb;
use Antares\Contracts\Html\Form\Factory;
use Antares\Tester\Builder\RoundRobin;
use Antares\Tester\Memory\Handler;
use Illuminate\Support\Fluent;
use Antares\Testing\TestCase;
use Antares\Memory\Provider;
use Mockery as m;

class CollectivePresenterTest extends TestCase
{

    /**
     * Construct
     * 
     * @test
     */
    public function testConstruct()
    {
        $roundRobin = m::mock(RoundRobin::class);
        $factory    = m::mock(Factory::class);
        $breadcrumb = m::mock(Breadcrumb::class);
        $this->assertInstanceOf(CollectivePresenter::class, new Stub($roundRobin, $factory, $breadcrumb));
    }

    /**
     * creating instance of dynamic form
     * 
     * @test
     */
    public function form()
    {
        $roundRobin     = m::mock(RoundRobin::class);
        $roundRobin->shouldReceive('build')->withNoArgs()->once()->andReturnSelf();
        $factory        = m::mock(Factory::class);
        $factory2       = m::mock(Factory::class);
        $fluent         = m::mock(Fluent::class);
        $factory2->grid = $fluent;
        $factory->shouldReceive('of')->with(m::type('String'), m::type('Closure'))->andReturn($factory2);
        $breadcrumb     = m::mock(Breadcrumb::class);
        $breadcrumb->shouldReceive('onForm')->withNoArgs()->once()->andReturnNull();

        $stub     = new Stub($roundRobin, $factory, $breadcrumb);
        $memory   = m::mock(Handler::class);
        $active   = [
            'domains/dns' => [
                'path'        => 'vendor::antares/modules/domains/dns',
                'source-path' => 'vendor::antares/modules/domains/dns',
                'name'        => 'domains/dns',
                'full_name'   => 'Dns Manager Module',
                'description' => 'Foo',
                'author'      => 'Foo Foo',
                'url'         => 'https://billevo.com/docs/dns',
                'version'     => '1.0.0',
                'config'      => [],
                'autoload'    => [],
                'provides'    => [
                    'Antares\Domains\Dns\DnsServiceProvider'
                ]
            ]
        ];
        $provider = m::mock(Provider::class);
        $tests    = [
            'Rackspace Module Configuration Test' =>
            [
                'component_id' => 12,
                'component'    => 'domains/dns',
                'controls'     =>
                [
                    'username'       => 'lukasz.cirut@inbs.software',
                    'api_access_key' => 'dsfsdfdf',
                    'access_key'     => 'testowanie',
                    'hostname'       => '123.123.123.123',
                    'ssl'            => 'on',
                    'default_ip'     => '123.123.123.123',
                    'create_zones'   => 'on'
                ],
                'name'         => 'Rackspace',
                'title'        => 'Rackspace Module Configuration Test',
                'validator'    => 'Antares\Domains\Dns\Tester\RackspaceTester',
                'executor'     => 'Antares\Domains\Dns\Http\Forms\RackspaceForm',
                'id'           => 15
            ]
        ];

        $provider->shouldReceive('all')->withNoArgs()->andReturn($tests)
                ->shouldReceive('finish')->withNoArgs()->andReturn($tests)
                ->shouldReceive('forget')->with('Rackspace Module Configuration Test')->once()->andReturnNull();
        $memory->shouldReceive('get')->with('extensions.active')->andReturn($active)
                ->shouldReceive('make')->with('tests')->andReturn($provider);

        $this->app['antares.memory'] = $memory;
        $this->assertInstanceOf(get_class($factory2), $stub->form());
    }

}

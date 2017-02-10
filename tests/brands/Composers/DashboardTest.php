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


namespace Antares\Brands\TestCase;

use Mockery as m;
use Antares\Brands\Composers\Dashboard;
use Antares\Testbench\TestCase;

class DashboardTest extends TestCase
{

    /**
     * test contructor
     */
    public function testConstruct()
    {

        $mock    = m::mock('Antares\Widget\WidgetManager');
        $builder = m::mock('Antares\Datatables\Html\Builder');
        $stub    = new Dashboard($mock, $builder);
        $this->assertEquals(get_class($stub), 'Antares\Brands\Composers\Dashboard');
    }

    /**
     * test composing
     */
    public function testCompose()
    {
//        $foundation               = m::mock('\Antares\Contracts\Foundation\Foundation');
//        $foundation->shouldReceive('handles')->with(m::type('String'), array())->andReturn('#url');
//        $this->app['antares.app'] = $foundation;
//        $mock                     = m::mock('Antares\Widget\WidgetManager');
//        $this->app['view']->addNamespace('antares/brands', realpath(base_path() . '../../../../components/brands'));
//        $html                     = m::mock('Collective\Html\HtmlBuilder');
//        $url                      = m::mock('Illuminate\Routing\UrlGenerator');
//        $form                     = m::mock('Antares\Html\Support\FormBuilder');
//
//        $builder = new \Antares\Datatables\Html\Builder($this->app['config'], $this->app['view'], $html, $url, $form);
//
//
//        $handlersPaneMock = m::mock('Antares\Widget\Handlers\Pane');
//        $handlersPaneMock->shouldReceive('add')
//                ->once()
//                ->with(m::type('String'))
//                ->andReturnSelf()
//                ->shouldReceive('attributes')
//                ->once()
//                ->with(m::type('Array'))
//                ->andReturnSelf()
//                ->shouldReceive('title')
//                ->once()
//                ->with(m::type('String'))
//                ->andReturnSelf()
//                ->shouldReceive('content')
//                ->once()
//                ->with(m::type('Illuminate\View\View'))
//                ->andReturnSelf();
//
//        $mock->shouldReceive('make')
//                ->once()
//                ->with(m::type('String'))
//                ->andReturn($handlersPaneMock);
//
//
//        $stub = new Dashboard($mock, $builder);
//        $this->assertNull($stub->compose());
    }

}

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

namespace Antares\Foundation\Http\Presenters\TestCase;

use Antares\Foundation\Http\Presenters\Presenter;
use Illuminate\Support\Facades\Facade;
use Illuminate\Container\Container;
use Mockery as m;

class PresenterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        Facade::clearResolvedInstances();
    }

    /**
     * Test Antares\Foundation\Http\Presenters\Presenter::handles()
     * method.
     *
     * @test
     */
    public function testHandlesMethod()
    {
        $app     = m::mock('\Illuminate\Container\Container', '\Illuminate\Contracts\Foundation\Application');
        $antares = m::mock('\Antares\Foundation\Foundation[handles]', [$app]);

        Container::setInstance($app);

        $app->shouldReceive('make')->once()->with('antares.app')->andReturn($antares);

        $antares->shouldReceive('handles')->with(m::type('String'), m::type('Array'))
                ->andReturnUsing(function ($s) {
                    return "foobar/{$s}";
                });

        $stub = new PresenterStub();
        $this->assertEquals('foobar/hello', $stub->handles('hello'));
    }

    /**
     * Test Antares\Foundation\Http\Presenters\Presenter::setupForm()
     * method.
     *
     * @test
     */
    public function testSetupFormMethod()
    {
        $form = m::mock('\Antares\Contracts\Html\Form\Grid');

        $form->shouldReceive('layout')->once()
                ->with('antares/foundation::components.form')->andReturnNull();

        $stub = new PresenterStub();
        $stub->setupForm($form);
    }

}

class PresenterStub extends Presenter
{
    
}

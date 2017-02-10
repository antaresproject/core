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


namespace Antares\Foundation\Http\Presenters\TestCase;

use Mockery as m;
use Illuminate\Support\Fluent;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Antares\Foundation\Http\Presenters\Account;

class AccountTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $app = new Container();

        $app['antares.app'] = m::mock('\Antares\Contracts\Foundation\Foundation');
        $app['translator']  = m::mock('\Illuminate\Translation\Translator')->makePartial();

        $app['antares.app']->shouldReceive('handles');
        $app['translator']->shouldReceive('trans');
        $events        = m::mock('SampleEvent');
        $events->shouldReceive('fire')->withAnyArgs()->andReturnNull();
        $app['events'] = $events;

        Facade::setFacadeApplication($app);
        Container::setInstance($app);
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Https\Presenters\Account::profileForm()
     * method.
     *
     * @test
     */
    public function testProfileFormMethod()
    {
        $form = m::mock('\Antares\Contracts\Html\Form\Factory');

        $grid     = m::mock('\Antares\Contracts\Html\Form\Grid');
        $fieldset = m::mock('\Antares\Contracts\Html\Form\Fieldset');
        $control  = m::mock('\Antares\Contracts\Html\Form\Control');

        $model = new Fluent();
        $stub  = new Account($form);

        $control->shouldReceive('label')->twice()->andReturnSelf();
        $fieldset->shouldReceive('control')->twice()->with('input:text', m::any())->andReturn($control);
        $grid->shouldReceive('setup')->once()->with($stub, 'foo', $model)->andReturnNull()
                ->shouldReceive('hidden')->once()->with('id')->andReturnNull()
                ->shouldReceive('tester')->once()->withAnyArgs()->andReturnNull()
                ->shouldReceive('fieldset')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($fieldset) {
                    $c($fieldset);
                });
        $form->shouldReceive('of')->once()
                ->with('antares.account', m::type('Closure'))
                ->andReturnUsing(function ($f, $c) use ($grid) {
                    $c($grid);

                    return 'foo';
                });

        $this->assertEquals('foo', $stub->profile($model, 'foo'));
    }

    /**
     * Test Antares\Foundation\Https\Presenters\Account::passwordForm()
     * method.
     *
     * @test
     */
    public function testPasswordFormMethod()
    {
        $grid     = m::mock('\Antares\Contracts\Html\Form\Grid');
        $fieldset = m::mock('\Antares\Contracts\Html\Form\Fieldset');
        $control  = m::mock('\Antares\Contracts\Html\Form\Control');
        $form     = m::mock('\Antares\Contracts\Html\Form\Factory');

        $model = new Fluent();
        $stub  = new Account($form);

        $control->shouldReceive('label')->times(3)->andReturnSelf();
        $fieldset->shouldReceive('control')->times(3)->with('input:password', m::any())->andReturn($control);
        $grid->shouldReceive('setup')->once()->with($stub, 'antares::account/password', $model)->andReturnNull()
                ->shouldReceive('hidden')->once()->with('id')->andReturnNull()
                ->shouldReceive('tester')->once()->withAnyArgs()->andReturnNull()
                ->shouldReceive('fieldset')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($fieldset) {
                    $c($fieldset);
                });
        $form->shouldReceive('of')->once()
                ->with('antares.account: password', m::type('Closure'))
                ->andReturnUsing(function ($f, $c) use ($grid) {
                    $c($grid);

                    return 'foo';
                });

        $this->assertEquals('foo', $stub->password($model, 'foo'));
    }

}

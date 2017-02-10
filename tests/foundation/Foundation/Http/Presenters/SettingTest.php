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
use Antares\Foundation\Http\Presenters\Setting;

class SettingTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $app = new Container();

        $app['antares.app']                       = m::mock('\Antares\Contracts\Foundation\Foundation');
        $app['translator']                        = m::mock('\Illuminate\Translation\Translator')->makePartial();
        $app['antares.app']->shouldReceive('handles');
        $app['translator']->shouldReceive('trans');
        $viewFactory                              = m::mock('Illuminate\Contracts\View\Factory');
        $viewFactory->shouldReceive('make')->andReturn();
        $app['Illuminate\Contracts\View\Factory'] = $viewFactory;

        $events        = m::mock('AccountSampleEvent');
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
        unset($this->app);

        m::close();
    }

    /**
     * Test Antares\Foundation\Http\Presenters\Setting::form()
     * method.
     *
     * @test
     */
    public function testFormMethod()
    {
        $app   = $this->app;
        $model = new Fluent([
            'email_password' => 123456,
        ]);

        $app['Illuminate\Contracts\View\Factory'] = m::mock('\Illuminate\Contracts\View\Factory');

        $form = m::mock('\Antares\Contracts\Html\Form\Factory');
        $grid = m::mock('\Antares\Contracts\Html\Form\Grid');

        $siteFieldset = m::mock('\Antares\Contracts\Html\Form\Fieldset');
        $siteControl  = m::mock('\Antares\Contracts\Html\Form\Control');

        $emailFieldset = m::mock('\Antares\Contracts\Html\Form\Fieldset');
        $emailControl  = m::mock('\Antares\Contracts\Html\Form\Control');
        $emailControl->shouldReceive('help')->with(NUll);
        $stub          = new Setting($form);

        $siteFieldset->shouldReceive('control')->times(3)->andReturn($siteControl);
        $siteControl->shouldReceive('label')->times(3)->andReturnSelf()
                ->shouldReceive('attributes')->andReturnSelf()
                ->shouldReceive('options')->once()->andReturnSelf();

        $emailFieldset->shouldReceive('control')->times(13)
                ->with(m::any(), m::any())->andReturn($emailControl);
        $emailControl->shouldReceive('label')->times(13)->andReturnSelf()
                ->shouldReceive('attributes')->once()->andReturnSelf()
                ->shouldReceive('options')->times(3)->andReturnSelf()
                ->shouldReceive('help')->with('email.password.help');

        $grid->shouldReceive('setup')->once()
                ->with($stub, 'antares::settings', $model)->andReturnNull()
                ->shouldReceive('fieldset')->once()
                ->with(trans('antares/foundation::label.settings.application'), m::type('Closure'))
                ->andReturnUsing(function ($t, $c) use ($siteFieldset) {
                    $c($siteFieldset);
                })
                ->shouldReceive('fieldset')->once()
                ->with(trans('antares/foundation::label.settings.mail'), m::type('Closure'))
                ->andReturnUsing(function ($t, $c) use ($emailFieldset) {
                    $c($emailFieldset);
                });

        $form->shouldReceive('of')->once()
                ->with('antares.settings', m::type('Closure'))
                ->andReturnUsing(function ($n, $c) use ($grid) {
                    $c($grid);

                    return 'foo';
                });

        $app['Illuminate\Contracts\View\Factory']->shouldReceive('make')
                ->with('antares/foundation::settings._hidden', m::type('Array'), [])
                ->andReturn('email.password.help');

        $this->assertEquals('foo', $stub->form($model));
    }

}

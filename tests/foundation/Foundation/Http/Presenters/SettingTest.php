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

use Antares\Foundation\Http\Presenters\Setting;
use Antares\Foundation\Http\Form\Settings;
use Antares\Testing\ApplicationTestCase;
use Illuminate\Support\Fluent;
use Mockery as m;

class SettingTest extends ApplicationTestCase
{

    /**
     * Test Antares\Foundation\Http\Presenters\Setting::form()
     * method.
     *
     * @test
     */
    public function testFormMethod()
    {
        $model = new Fluent([
            'email_password' => 123456,
        ]);

        $breadcrumb = m::mock('Antares\Foundation\Http\Breadcrumb\Breadcrumb');
        $breadcrumb->shouldReceive('onSettings')->andReturnSelf();

        $this->app['Antares\Contracts\Html\Form\Control'] = $control                                          = m::mock('Antares\Contracts\Html\Form\Control');
        $control->shouldReceive('setTemplates')->andReturnSelf()
                ->shouldReceive('setPresenter')->with(m::type('Object'))->andReturnSelf()
                ->shouldReceive('generate')->with(m::type('String'))->andReturnUsing(function() {
            return 'foo';
        });
        $this->app['Antares\Contracts\Html\Form\Template'] = m::mock('Antares\Contracts\Html\Form\Template');
        $stub                                              = new Setting($breadcrumb);
        $this->assertInstanceOf(Settings::class, $stub->form($model));
    }

}

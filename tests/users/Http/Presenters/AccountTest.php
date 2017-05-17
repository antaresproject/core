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

namespace Antares\Users\Http\Presenters\TestCase;

use Antares\Users\Http\Presenters\Account;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class AccountTest extends ApplicationTestCase
{

    /**
     * Test Antares\Users\Https\Presenters\Account::profileForm()
     * method.
     *
     * @test
     */
    public function testProfileFormMethod()
    {
        $this->app->instance(\Illuminate\Contracts\View\Factory::class, $factory = m::mock(\Illuminate\View\Factory::class));
        $factory->shouldReceive('make')->andReturnSelf()
                ->shouldReceive('render')->andReturn('rendered')
                ->shouldReceive('share')->andReturnSelf();

        $form = m::mock('\Antares\Contracts\Html\Form\Factory');

        $breadcrumb = m::mock('\Antares\Users\Http\Breadcrumb\Breadcrumb');
        $breadcrumb->shouldReceive('onAccount')->andReturnSelf();
        $model      = new \Antares\Model\User();

        $stub = new Account($form, $breadcrumb);
        $this->assertInstanceOf(\Antares\Html\Form\FormBuilder::class, $stub->profile($model));
    }

    /**
     * Test Antares\Users\Https\Presenters\Account::passwordForm()
     * method.
     *
     * @test
     */
    public function testPasswordFormMethod()
    {

        $this->app->instance(\Illuminate\Contracts\View\Factory::class, $factory  = m::mock(\Illuminate\View\Factory::class));
        $model    = new \Antares\Model\User();
        $factory->shouldReceive('make')->andReturnSelf()
                ->shouldReceive('render')->andReturn('rendered')
                ->shouldReceive('share')->andReturnSelf();
        $fieldset = m::mock('\Antares\Contracts\Html\Form\Fieldset');
        $control  = m::mock('\Antares\Contracts\Html\Form\Control');
        $control->shouldReceive('label')->times(3)->andReturnSelf();
        $fieldset->shouldReceive('control')->times(3)->with('input:password', m::any())->andReturn($control);

        $grid = m::mock('\Antares\Contracts\Html\Form\Grid');
        $grid->shouldReceive('setup')->once()->andReturnNull()
                ->shouldReceive('hidden')->once()->with('id')->andReturnNull()
                ->shouldReceive('tester')->once()->withAnyArgs()->andReturnNull()
                ->shouldReceive('fieldset')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($fieldset) {
                    $c($fieldset);
                });

        $form = m::mock('\Antares\Contracts\Html\Form\Factory');
        $form->shouldReceive('of')->once()
                ->with('antares.account: password', m::type('Closure'))
                ->andReturnUsing(function ($f, $c) use ($grid) {
                    $c($grid);

                    return 'foo';
                });
        $breadcrumb = m::mock('\Antares\Users\Http\Breadcrumb\Breadcrumb');
        $breadcrumb->shouldReceive('onAccount')->andReturnSelf();


        $stub = new Account($form, $breadcrumb);
        $this->assertEquals('foo', $stub->password($model));
    }

}

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

namespace Antares\Users\Http\Presenters\TestCase;

use Antares\Users\Http\Presenters\User;
use Illuminate\Support\Facades\Facade;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Mockery as m;

class UserTest extends \PHPUnit_Framework_TestCase
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
        $this->app = new Container();

        $this->app['app']         = $this->app;
        $this->app['antares.app'] = m::mock('\Antares\Contracts\Foundation\Foundation');
        $this->app['translator']  = m::mock('\Illuminate\Translation\Translator')->makePartial();

        $this->app['antares.app']->shouldReceive('handles');
        $this->app['translator']->shouldReceive('trans');

        $events        = m::mock('UserSampleEvent');
        $events->shouldReceive('fire')->withAnyArgs()->andReturnNull();
        $app['events'] = $events;

        Facade::setFacadeApplication($app);
        Container::setInstance($this->app);
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
     * Test Antares\Users\Http\Presenters\User::form() method.
     *
     * @test
     */
    public function testFormMethod()
    {
        $app   = $this->app;
        $model = m::mock('\Antares\Model\User');

        $auth  = m::mock('\Illuminate\Contracts\Auth\Guard');
        $user  = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $form  = m::mock('\Antares\Contracts\Html\Form\Factory');
        $table = m::mock('\Antares\Contracts\Html\Table\Factory');

        $grid     = m::mock('\Antares\Contracts\Html\Form\Grid');
        $fieldset = m::mock('\Antares\Contracts\Html\Form\Fieldset');
        $control  = m::mock('\Antares\Contracts\Html\Form\Control');

        $app['Antares\Contracts\Html\Form\Control'] = $control;
        $app['antares.role']                        = m::mock('\Antares\Model\Role');

        $value = (object) [
                    'roles' => new Collection([
                        new Fluent(['id' => 1, 'name' => 'Administrator']),
                        new Fluent(['id' => 2, 'name' => 'Member']),
                            ]),
        ];

        $model->shouldReceive('hasGetMutator')->andReturn(false);

        $auth->shouldReceive('user')->once()->andReturn($user);

        $stub = new User($auth, $form, $table);

        $control->shouldReceive('label')->times(4)->andReturnSelf()
                ->shouldReceive('options')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($control) {
                    $c();

                    return $control;
                })
                ->shouldReceive('attributes')->once()->with(m::type('Array'))->andReturnSelf()
                ->shouldReceive('value')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($value) {
                    $c($value);
                });
        $fieldset->shouldReceive('control')->twice()->with('input:text', m::any())->andReturn($control)
                ->shouldReceive('control')->once()->with('input:password', 'password')->andReturn($control)
                ->shouldReceive('control')->once()->with('select', 'roles[]')->andReturn($control);
        $grid->shouldReceive('resource')->once()
                ->with($stub, 'antares/foundation::users', $model)->andReturnNull()
                ->shouldReceive('hidden')->once()->with('id')->andReturnNull()
                ->shouldReceive('fieldset')->once()->with(m::type('Closure'))
                ->andReturnUsing(function ($c) use ($fieldset) {
                    $c($fieldset);
                });
        $form->shouldReceive('of')->once()
                ->with('antares.users', m::any())
                ->andReturnUsing(function ($f, $c) use ($grid) {
                    $c($grid);

                    return 'foo';
                });

        $app['antares.role']->shouldReceive('lists')->once()
                ->with('name', 'id')->andReturn('roles');

        $stub->form($model);
    }

}

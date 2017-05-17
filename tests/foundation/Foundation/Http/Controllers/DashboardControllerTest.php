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

namespace Antares\Foundation\Http\Controllers\TestCase;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Testing\ApplicationTestCase;
use Illuminate\Support\Facades\View;
use Mockery as m;

class DashboardControllerTest extends ApplicationTestCase
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
     * Test GET /antares.
     *
     * @test
     */
    public function testIndexAction()
    {
        $this->app[\Illuminate\Contracts\Auth\Factory::class] = $auth                                                 = m::mock(\Illuminate\Contracts\Auth\Factory::class);
        $auth->shouldReceive('guest')->times(3)->andReturn(false);
        $auth->shouldReceive('user')->once()->andReturn(\Antares\Model\User::query()->whereId(1)->first());


        View::shouldReceive('make')->once()->with('antares/foundation::dashboard.index', ['panes' => 'foo'], [])->andReturn('foo')
                ->shouldReceive('share')->once()->with(m::type('string'), m::type('string'))->andReturnSelf()
                ->shouldReceive('addNamespace')->once()->with(m::type('string'), m::type('string'))->andReturnSelf()
                ->shouldReceive('make')->once()->withAnyArgs()->andReturnSelf()
                ->shouldReceive('render')->once()->withNoArgs()->andReturn('foo');

        $this->call('GET', 'antares');
        $this->assertResponseOk();
    }

    /**
     * Test GET /antares/missing.
     *
     */
    public function testMissingAction()
    {
        $this->call('GET', 'antares/missing');
        $this->assertResponseStatus(404);
    }

}

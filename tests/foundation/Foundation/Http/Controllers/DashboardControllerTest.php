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

    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    /**
     * Test GET /antares.
     *
     * @test
     */
    public function testIndexAction()
    {
        $this->app[\Illuminate\Contracts\Auth\Factory::class] = $auth = m::mock(\Illuminate\Contracts\Auth\Factory::class);

        $auth->shouldReceive('guest')->andReturn(false);
        $auth->shouldReceive('user')->andReturn(\Antares\Model\User::query()->whereId(1)->first());

        $response = $this->get('/');

        $response->assertSuccessful();
    }

    /**
     * Test GET /antares/missing.
     *
     */
    public function testMissingAction()
    {
        $response =  $this->get('antares/missing');
        $response->assertStatus(404);
    }

}

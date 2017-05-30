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

use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Foundation\Http\Controllers\BaseController;

class BaseControllerTest extends TestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->disableMiddlewareForAllTests();

        $_SERVER['StubBaseController@setupFilters'] = false;
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($_SERVER['StubBaseController@setupFilters']);
    }

    /**
     * Test Antares\Foundation\Http\Controllers\BaseController::missingMethod()
     * action.
     *
     * @test
     */
    public function testMissingMethodAction()
    {
        $app        = new Container();
        $factory    = m::mock('\Illuminate\Contracts\View\Factory');
        $view       = m::mock('\Illuminate\Contracts\View\View');
        $redirector = m::mock('\Illuminate\Routing\Redirector');
        $response   = m::mock('\Illuminate\Routing\ResponseFactory', [$factory, $redirector]);

        $app['Illuminate\Contracts\Routing\ResponseFactory'] = $response;

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
        Container::setInstance($app);

        $response->shouldReceive('view')->once()
                ->with('antares/foundation::dashboard.missing', [], 404)->andReturn($view);

        $this->assertFalse($_SERVER['StubBaseController@setupFilters']);

        $stub = new StubBaseController();

        $this->assertEquals($view, $stub->missingMethod([]));
        $this->assertTrue($_SERVER['StubBaseController@setupFilters']);
    }

}

class StubBaseController extends BaseController
{

    /**
     * Setup controller filters.
     */
    protected function setupFilters()
    {
        $_SERVER['StubBaseController@setupFilters'] = true;
    }

    public function setupMiddleware()
    {
        ;
    }

}

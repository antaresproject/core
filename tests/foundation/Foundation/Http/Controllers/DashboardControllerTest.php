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


namespace Antares\Foundation\Http\Controllers\TestCase;

use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class DashboardControllerTest extends TestCase
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
     * Test GET /admin.
     *
     * @test
     */
    public function testIndexAction()
    {
        $this->getProcessorMock()->shouldReceive('show')->once()
                ->andReturnUsing(function ($listener) {
                    return $listener->showDashboard(['panes' => 'foo']);
                });

        View::shouldReceive('make')->once()
                ->with('antares/foundation::dashboard.index', ['panes' => 'foo'], [])->andReturn('foo');

        $this->call('GET', 'admin');
        $this->assertResponseOk();
    }

    /**
     * Test GET /admin/missing.
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testMissingAction()
    {
        $this->call('GET', 'admin/missing');
    }

    /**
     * Get processor mock.
     *
     * @return \Antares\Users\Processor\Account\ProfileDashboard
     */
    protected function getProcessorMock()
    {
        $processor = m::mock('\Antares\Foundation\Processor\Account\ProfileDashboard', [
                    m::mock('\Antares\Widget\WidgetManager'),
        ]);

        $this->app->instance('Antares\Foundation\Processor\Account\ProfileDashboard', $processor);

        return $processor;
    }

}

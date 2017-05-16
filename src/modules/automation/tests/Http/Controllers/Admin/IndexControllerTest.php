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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\Http\Controllers\Admin\TestCase;

use Antares\Automation\Http\Presenters\IndexPresenter;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Automation\AutomationServiceProvider;
use Antares\Automation\Processor\IndexProcessor;
use Antares\Testing\ApplicationTestCase;
use Illuminate\View\View;
use Mockery as m;

class IndexControllerTest extends ApplicationTestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->addProvider(AutomationServiceProvider::class);
        parent::setUp();

        $this->disableMiddlewareForAllTests();
    }

    /**
     * Get processor mock.
     *
     * @return \Antares\Foundation\Processor\Account\ProfileDashboard
     */
    protected function getProcessorMock()
    {
        $kernel    = m::mock(\Illuminate\Contracts\Console\Kernel::class);
        $processor = m::mock(IndexProcessor::class, [m::mock(IndexPresenter::class), $kernel]);
        $processor->shouldReceive('update')->withAnyArgs()->andReturnNull();
        $this->app->instance(IndexProcessor::class, $processor);
        return $processor;
    }

    /**
     * Tests Antares\Automation\Http\Controllers\Admin\IndexController::index
     */
    public function testIndex()
    {
        $this->getProcessorMock()->shouldReceive('index')->once()->andReturn(View::class);
        $this->call('GET', 'antares/automation/index');
        $this->assertResponseOk();
    }

    /**
     * Tests Antares\Automation\Http\Controllers\Admin\IndexController::show
     */
    public function testShow()
    {
        $this->getProcessorMock()->shouldReceive('show')->once()->andReturn(View::class);
        $this->call('GET', 'antares/automation/show/1');
        $this->assertResponseOk();
    }

    /**
     * Tests Antares\Automation\Http\Controllers\Admin\IndexController::showFailed
     */
    public function testShowFailed()
    {
        $this->getProcessorMock()->shouldReceive('show')->once()
                ->andReturnUsing(function ($request, $listener) {
                    return $listener->showFailed();
                });
        $this->call('GET', 'antares/automation/show/1');
        $this->assertResponseStatus(302);
    }

    /**
     * Tests Antares\Automation\Http\Controllers\Admin\IndexController::edit
     */
    public function testEdit()
    {
        $this->getProcessorMock()->shouldReceive('edit')->once()->andReturn(View::class);
        $this->call('GET', 'antares/automation/edit/1');
        $this->assertResponseOk();
    }

    /**
     * Tests Antares\Automation\Http\Controllers\Admin\IndexController::update
     */
    public function testUpdate()
    {
        $this->getProcessorMock()->shouldReceive('edit')->once()->andReturnUsing(function ($id, $listener) {
            return $listener->updateSuccess();
        });
        $this->call('POST', 'antares/automation/update');
        $this->assertResponseStatus(200);
    }

    /**
     * Tests Antares\Automation\Http\Controllers\Admin\IndexController::updateFailed
     */
    public function testUpdateFailed()
    {
        $this->getProcessorMock()->shouldReceive('edit')->once()->andReturnUsing(function ($id, $listener) {
            return $listener->updateFailed();
        });
        $this->call('POST', 'antares/automation/update');
        $this->assertResponseStatus(200);
    }

    /**
     * Tests Antares\Automation\Http\Controllers\Admin\IndexController::updateSuccess
     */
    public function testUpdateSuccess()
    {
        $this->getProcessorMock()->shouldReceive('edit')->once()->andReturnUsing(function ($id, $listener) {
            return $listener->updateSuccess();
        });
        $this->call('POST', 'antares/automation/update');
        $this->assertResponseStatus(200);
    }

    /**
     * Tests Antares\Automation\Http\Controllers\Admin\IndexController::run
     */
    public function testRun()
    {
        $this->getProcessorMock()->shouldReceive('run')->once()->andReturnUsing(function ($id, $listener) {
            return $listener->updateSuccess();
        });
        $this->call('GET', 'antares/automation/run/1');
        $this->assertResponseStatus(302);
    }

    /**
     * Tests Antares\Automation\Http\Controllers\Admin\IndexController::runSuccess
     */
    public function testRunSuccess()
    {
        $this->getProcessorMock()->shouldReceive('run')->once()->andReturnUsing(function ($id, $listener) {
            return $listener->updateSuccess();
        });
        $this->call('GET', 'antares/automation/run/1');
        $this->assertResponseStatus(302);
    }

    /**
     * Tests Antares\Automation\Http\Controllers\Admin\IndexController::runFailed
     */
    public function testRunFailed()
    {
        $this->getProcessorMock()->shouldReceive('run')->once()->andReturnUsing(function ($id, $listener) {
            return $listener->updateFailed();
        });
        $this->call('GET', 'antares/automation/run/1');
        $this->assertResponseStatus(302);
    }

}

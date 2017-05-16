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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Http\Controllers\Admin\TestCase;

use Antares\Notifications\Http\Presenters\IndexPresenter;
use Antares\Notifications\NotificationsServiceProvider;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Notifications\Adapter\VariablesAdapter;
use Antares\Notifications\Processor\IndexProcessor;
use Antares\Notifications\Repository\Repository;
use Antares\Notifications\Model\Notifications;
use Antares\Testing\TestCase;
use Illuminate\View\View;
use Mockery as m;

class IndexControllerTest extends TestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->addProvider(NotificationsServiceProvider::class);
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
        $variablesAdapter = $this->app->make(VariablesAdapter::class);
        $repository       = m::mock(Repository::class);
        $repository->shouldReceive('find')->once()->andReturn(new Notifications())
                ->shouldReceive('findByLocale')->once()->andReturn(new Notifications());

        $processor = m::mock(IndexProcessor::class, [m::mock(IndexPresenter::class), $variablesAdapter, $repository]);
        $processor->shouldReceive('update')->withAnyArgs()->andReturnNull();
        $this->app->instance(IndexProcessor::class, $processor);
        return $processor;
    }

    /**
     * Tests Antares\Notifications\Http\Controllers\Admin\IndexController::index
     */
    public function testIndex()
    {
        $this->getProcessorMock()->shouldReceive('index')->once()->andReturn(View::class);
        $this->call('GET', 'antares/notifications/index');
        $this->assertResponseOk();
    }

    /**
     * Tests Antares\Notifications\Http\Controllers\Admin\IndexController::edit
     */
    public function testEdit()
    {
        $this->getProcessorMock()->shouldReceive('edit')->once()->andReturn(View::class);
        $this->call('GET', 'antares/notifications/edit/1');
        $this->assertResponseOk();
    }

    /**
     * Tests Antares\Notifications\Http\Controllers\Admin\IndexController::update
     */
    public function testUpdate()
    {
        $this->getProcessorMock()->shouldReceive('edit')->once()->andReturnUsing(function ($id, $listener) {
            return $listener->updateSuccess();
        });
        $this->call('POST', 'antares/notifications/update');
        $this->assertResponseStatus(200);
    }

    /**
     * Tests Antares\Notifications\Http\Controllers\Admin\IndexController::updateFailed
     */
    public function testUpdateFailed()
    {
        $this->getProcessorMock()->shouldReceive('edit')->once()->andReturnUsing(function ($id, $listener) {
            return $listener->updateFailed();
        });
        $this->call('POST', 'antares/notifications/update');
        $this->assertResponseStatus(200);
    }

    /**
     * Tests Antares\Notifications\Http\Controllers\Admin\IndexController::updateSuccess
     */
    public function testUpdateSuccess()
    {
        $this->getProcessorMock()->shouldReceive('edit')->once()->andReturnUsing(function ($id, $listener) {
            return $listener->updateSuccess();
        });
        $this->call('POST', 'antares/notifications/update');
        $this->assertResponseStatus(200);
    }

}

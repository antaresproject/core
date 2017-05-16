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

namespace Antares\Notifications\Processor\TestCase;

use Antares\Notifications\Http\Datatables\Notifications as Datatables;
use Antares\Notifications\Http\Presenters\IndexPresenter;
use Antares\Notifications\Http\Presenters\Breadcrumb;
use Antares\Notifications\Adapter\VariablesAdapter;
use Antares\Notifications\Processor\IndexProcessor;
use Antares\Notifications\Contracts\IndexListener;
use Antares\Notifications\Repository\Repository;
use Antares\Notifications\Model\Notifications;
use Illuminate\Http\RedirectResponse;
use Antares\Testing\TestCase;
use Illuminate\View\View;
use Mockery as m;

class IndexProcessorTest extends TestCase
{

    /**
     * create presenter stub instance
     * 
     * @return IndexPresenter
     */
    protected function getPresenter()
    {
        $this->app['view']->addNamespace('antares/notifications', realpath(base_path() . '../../../../components/notifications/resources/views'));
        $breadcrumb = m::mock(Breadcrumb::class);
        $breadcrumb
                ->shouldReceive('onTable')->with(m::type('Object'))->once()->andReturnNull()
                ->shouldReceive('onEdit')->with(m::type('Object'))->once()->andReturnNull();
        $datatable  = $this->app->make(Datatables::class);

        return new IndexPresenter($breadcrumb, $datatable);
    }

    /**
     * gets stub instance
     * 
     * @return IndexProcessor
     */
    protected function getStub()
    {
        $variablesAdapter = $this->app->make(VariablesAdapter::class);
        $repository       = m::mock(Repository::class);
        $repository->shouldReceive('find')->once()->andReturn(new Notifications())
                ->shouldReceive('findByLocale')->once()->andReturn(new Notifications());
        return new IndexProcessor($this->getPresenter(), $variablesAdapter, $repository);
    }

    /**
     * Tests Antares\Notifications\Processor\IndexProcessor::index
     */
    public function testIndex()
    {
        $stub = $this->getStub();
        $this->assertInstanceOf(View::class, $stub->index($this->app['request']));
    }

    /**
     * Tests Antares\Notifications\Processor\IndexProcessor::edit
     */
    public function testEdit()
    {
        $stub          = $this->getStub();
        $indexListener = m::mock(IndexListener::class);
        $this->assertInstanceOf(View::class, $stub->edit(0, 'en', $indexListener));
    }

    /**
     * Tests Antares\Notifications\Processor\IndexProcessor::update
     */
    public function testUpdate()
    {
        $stub          = $this->getStub();
        $indexListener = m::mock(IndexListener::class);
        $indexListener
                ->shouldReceive('updateSuccess')->andReturn(new RedirectResponse('#'))
                ->shouldReceive('updateFailed')->andReturn(new RedirectResponse('#'));
        $this->assertInstanceOf(RedirectResponse::class, $stub->update($indexListener));
    }

}

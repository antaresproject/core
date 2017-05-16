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

namespace Antares\Automation\Processor\TestCase;

use Antares\Automation\Contracts\IndexListener;
use Antares\Automation\Http\Presenters\IndexPresenter;
use Antares\Automation\Processor\IndexProcessor;
use Antares\Datatables\Html\Builder as Builder2;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Builder as Builder3;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;
use function base_path;
use function url;

class IndexProcessorTest extends ApplicationTestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
//        $model = m::mock('Antares\Automation\Model\Jobs');
//
//
//        $resolver   = m::mock('Illuminate\Database\ConnectionInterface')
//                        ->shouldReceive('getTablePrefix')->withNoArgs()->andReturn('dupa')
//                        ->shouldReceive('getDriverName')->withNoArgs()->andReturn('mysql')
//                        ->shouldReceive('getQueryGrammar')
//                        ->andReturn($this->app['Illuminate\Database\Query\Grammars\Grammar'])
//                        ->shouldReceive('raw')
//                        ->andReturn($expression = m::mock('Illuminate\Database\Query\Expression'))
//                        ->shouldReceive('select')
//                        ->withAnyArgs()
//                        ->andReturn(null)->getMock();
//
//        $expression->shouldReceive('getValue')->andReturn('testowanie');
//
//        $queryBuilder = m::mock(Builder::class);
//        $queryBuilder->shouldReceive('getConnection')->withNoArgs()->andReturn($resolver)
//                ->shouldReceive('toSql')->withNoArgs()->andReturn('')
//                ->shouldReceive('select')->withAnyArgs()->andReturn(null)
//                ->shouldReceive('getBindings')->withAnyArgs()->andReturn([])
//                ->shouldReceive('setBindings')->withAnyArgs()->andReturnSelf()
//                ->shouldReceive('count')->withAnyArgs()->andReturn(0)
//                ->shouldReceive('get')->withAnyArgs()->andReturn([1 => 2])
//                ->shouldReceive('from')->with('tbl_jobs')->andReturnSelf();
//
//
//        $resolver->shouldReceive('table')
//                ->andReturn($queryBuilder);
//
//
//        $builder = new Builder3($queryBuilder);
//        $model->shouldReceive('getTable')->withNoArgs()->andReturn('tbl_jobs')
//                ->shouldReceive('get')->withAnyArgs()->andReturn($builder)
//                ->shouldReceive('getConnectionName')->withAnyArgs()->andReturn('mysql')
//                ->shouldReceive('hydrate')->withAnyArgs()->andReturn(new Collection([1 => 2]))
//                ->shouldReceive('where')->withAnyArgs()->andReturnSelf()
//                ->shouldReceive('first')->withNoArgs()->andReturnSelf()
//                ->shouldReceive('delete')->withNoArgs()->andReturnSelf()
//                ->shouldReceive('with')->withAnyArgs()->andReturnSelf()
//                ->shouldReceive('query')->withNoArgs()->andReturn($builder)
//                ->shouldReceive('getAttribute')->with('jobResults')->andReturn(new Collection())
//                ->shouldReceive('getAttribute')->with('value')->andReturn(serialize(['foo' => 1, 'active' => 1, 'classname' => '\Antares\Logger\Console\ReportCommand', 'launch' => 'everyMinute', 'launchTimes' => ['everyMinute']]))
//                ->shouldReceive('with')->with(m::type('String'))->andReturnSelf()
//                ->shouldReceive('setAttribute')->withAnyArgs()->andReturnSelf()
//                ->shouldReceive('save')->withNoArgs()->andReturnSelf()
//                ->shouldReceive('toArray')->withNoArgs()->andReturn(['foo' => 1])
//                ->shouldReceive('select')->withAnyArgs()->andReturnSelf()
//                ->shouldReceive('take')->withAnyArgs()->andReturnSelf()
//                ->shouldReceive('skip')->withAnyArgs()->andReturnSelf()
//                ->shouldReceive('toSql')->withNoArgs()->andReturn('select')
//                ->shouldReceive('getQuery')->withNoArgs()->andReturn($builder)
//                ->shouldReceive('newCollection')->withAnyArgs()->andReturn(new Collection());
//
//        $this->app['Antares\Automation\Model\Jobs'] = $model;
//
//        $builder->setModel($model);
//        $model->shouldReceive('all')->withNoArgs()->andReturn($builder);
//
        $this->app['view']->addNamespace('antares/automation', realpath(base_path() . '../../../../components/automation/resources/views'));
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * create presenter stub instance
     * 
     * @return IndexPresenter
     */
    protected function getPresenter()
    {

        $breadcrumb = m::mock(\Antares\Automation\Http\Breadcrumb\Breadcrumb::class);
        $breadcrumb->shouldReceive('onList')->withNoArgs()->andReturn(null)
                ->shouldReceive('onShow')->withAnyArgs()->andReturn(null)
                ->shouldReceive('onEdit')->withAnyArgs()->andReturn(null);

        $automationDatatables        = $this->app->make(\Antares\Automation\Http\Datatables\Automation::class);
        $automationDetailsDatatables = $this->app->make(\Antares\Automation\Http\Datatables\AutomationDetails::class);
        return new IndexPresenter($this->app, $breadcrumb, $automationDatatables, $automationDetailsDatatables);
    }

    /**
     * gets stub instance
     * 
     * @return IndexProcessor
     */
    protected function getStub()
    {
        $kernel = m::mock(Kernel::class);
        return new IndexProcessor($this->getPresenter(), $kernel);
    }

    /**
     * Tests Antares\Automation\Processor\IndexProcessor::index
     */
    public function testIndex()
    {
        $stub = $this->getStub();
        $this->assertInstanceOf(View::class, $stub->index($this->app['request']));
    }

    /**
     * Tests Antares\Automation\Processor\IndexProcessor::show
     */
    public function testShow()
    {
        $stub = $this->getStub();
        $this->assertInstanceOf(View::class, $stub->show(1));
    }

    /**
     * Tests Antares\Automation\Processor\IndexProcessor::edit
     */
    public function testEdit()
    {
        $stub          = $this->getStub();
        $indexListener = m::mock(IndexListener::class);
        $this->assertInstanceOf(View::class, $stub->edit(1, $indexListener));
    }

    /**
     * Tests Antares\Automation\Processor\IndexProcessor::update
     * 
     * @expectedException  \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function testUpdate()
    {
        $stub          = $this->getStub();
        $indexListener = m::mock(IndexListener::class);
        $indexListener->shouldReceive('updateSuccess')->andReturn(new RedirectResponse('#'));
        $this->assertInstanceOf(RedirectResponse::class, $stub->update($indexListener));
    }

}

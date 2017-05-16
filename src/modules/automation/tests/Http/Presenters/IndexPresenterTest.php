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



namespace Antares\Automation\Http\Presenters\TestCase;

use Antares\Automation\Http\Presenters\IndexPresenter;
use Antares\Datatables\Html\Builder;
use Antares\Testing\TestCase;
use Exception;
use Illuminate\Database\Eloquent\Builder as Builder3;
use Illuminate\Database\Query\Builder as Builder2;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mockery as m;
use function base_path;
use function url;

class IndexPresenterTest extends TestCase
{

    protected $stub;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $htmlBuilder  = $this->app->make('Collective\Html\HtmlBuilder');
        $formBuilder  = $this->app->make('Antares\Html\Support\FormBuilder');
        $urlGenerator = url();
        $builder      = new Builder($this->app['config'], $this->app['view'], $htmlBuilder, $urlGenerator, $formBuilder);
        $this->stub   = new IndexPresenter($this->app, $builder);
        $this->app['view']->addNamespace('antares/automation', realpath(base_path() . '../../../../components/automation/resources/views'));
    }

    protected function getModel()
    {
        return m::mock('Antares\Automation\Model\Jobs')
                        ->shouldReceive('getTable')->withNoArgs()->andReturn('tbl_jobs')
                        ->shouldReceive('getConnectionName')->withAnyArgs()->andReturn('mysql')
                        ->shouldReceive('hydrate')->withAnyArgs()->andReturn(new Collection([1 => 2]))
                        ->shouldReceive('where')->withAnyArgs()->andReturnSelf()
                        ->shouldReceive('first')->withNoArgs()->andReturnSelf()
                        ->shouldReceive('delete')->withNoArgs()->andReturnSelf()
                        ->shouldReceive('newCollection')->withAnyArgs()->andReturn(new Collection())
                        ->getMock();
    }

    protected function getBuilder()
    {
        $resolver   = m::mock('Illuminate\Database\ConnectionInterface')
                        ->shouldReceive('getTablePrefix')->withNoArgs()->andReturn('dupa')
                        ->shouldReceive('getDriverName')->withNoArgs()->andReturn('mysql')
                        ->shouldReceive('getQueryGrammar')
                        ->andReturn($this->app['Illuminate\Database\Query\Grammars\Grammar'])
                        ->shouldReceive('raw')
                        ->andReturn($expression = m::mock('Illuminate\Database\Query\Expression'))
                        ->shouldReceive('select')
                        ->once()
                        ->withAnyArgs()
                        ->andReturn(null)->getMock();

        $expression->shouldReceive('getValue')->andReturn('testowanie');

        $queryBuilder = m::mock(Builder2::class);
        $queryBuilder->shouldReceive('getConnection')->withNoArgs()->andReturn($resolver)
                ->shouldReceive('toSql')->withNoArgs()->andReturn('')
                ->shouldReceive('select')->once()->withAnyArgs()->andReturn(null)
                ->shouldReceive('getBindings')->once()->withAnyArgs()->andReturn([])
                ->shouldReceive('setBindings')->once()->withAnyArgs()->andReturnSelf()
                ->shouldReceive('count')->once()->withAnyArgs()->andReturn(0)
                ->shouldReceive('get')->once()->withAnyArgs()->andReturn([1 => 2])
                ->shouldReceive('from')->once()->with('tbl_jobs')->andReturnSelf();


        $resolver->shouldReceive('table')
                ->andReturn($queryBuilder);
        $builder = new Builder3($queryBuilder);
        $model   = $this->getModel();
        $model->shouldReceive('get')->withAnyArgs()->andReturn($builder);
        $builder->setModel($model);
        return $builder;
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Tests Antares\Automation\Http\Presenters\IndexPresenter::show
     */
    public function testShow()
    {
        $this->assertInstanceOf(View::class, $this->stub->show($this->getModel()));
    }

    /**
     * Tests Antares\Automation\Http\Presenters\IndexPresenter::table
     */
    public function testTable()
    {
        $this->assertInstanceOf(View::class, $this->stub->table($this->getBuilder()));
    }

    /**
     * Tests Antares\Automation\Http\Presenters\IndexPresenter::tableJson
     */
    public function testTableJson()
    {
        $this->assertInstanceOf(JsonResponse::class, $this->stub->tableJson($this->getBuilder()));
    }

    /**
     * Tests Antares\Automation\Http\Presenters\IndexPresenter::view
     */
    public function testView()
    {
        try {
            $this->stub->view('foo', [], []);
        } catch (Exception $e) {
            $this->assertSame("View [admin.index.foo] not found.", $e->getMessage());
        }
        $this->assertInstanceOf(View::class, $this->stub->view('index', []));
    }

}

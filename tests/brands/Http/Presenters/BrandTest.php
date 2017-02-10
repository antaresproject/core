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


namespace Antares\Brands\Presenters\TestCase;

use Antares\Testbench\TestCase;
use Mockery as m;
use Antares\Brands\Http\Presenters\Brand;
use Antares\Memory\Model as Eloquent;
use Illuminate\Container\Container;
use Antares\Brands\Model\Brands;
use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Illuminate\Database\Query\Builder;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class BrandTest extends TestCase
{

    use EloquentConnectionTrait;

    /**
     * @var Antares\Brands\Http\Presenters\Brand
     */
    protected $stub;

    /**
     * setup
     */
    public function setUp()
    {
        parent::setUp();
        $container    = m::mock('Illuminate\Container\Container');
        $container->shouldReceive('make')->with('antares.asset')
                ->andReturn($assetFactory = m::mock('Antares\Asset\Factory'))
                ->shouldReceive('make')->with('config')
                ->andReturn($this->app['config']);
        $assetFactory->shouldReceive('container')->with(m::type("String"))
                ->andReturn($asset        = m::mock('Antares\Asset\Asset'));


        $asset->shouldReceive('get')->with(m::type('String'))->andReturnNull();

        $memory                               = m::mock('Antares\Memory\MemoryManager');
        $mock                                 = m::mock('\Antares\Contracts\Html\Form\Factory');
        $mock->shouldReceive('of')
                ->with(m::type('String'), m::type('Closure'))
                ->andReturn(true)
                ->shouldReceive('build')
                ->andReturn(true);
        $this->stub                           = new Brand($mock, $memory, $container);
        $this->app['antares.platform.memory'] = m::mock('Antares\Memory\Provider');

        $acl = m::mock('Antares\Authorization\Factory')
                ->shouldReceive('make')
                ->with("antares/widgets")
                ->andReturnSelf()
                ->shouldReceive('make')
                ->with("antares/brands")
                ->andReturnSelf()
                ->shouldReceive("can")
                ->with(m::type("String"))
                ->andReturn(true)
                ->shouldReceive('attach')
                ->with($this->app['antares.platform.memory'])
                ->andReturnSelf()
                ->getMock();


        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $model      = new Brands();
        $this->addMockConnection($model);


        $resolver = m::mock('Illuminate\Database\ConnectionResolverInterface');
        $model->setConnectionResolver($resolver);

        $resolver->shouldReceive('connection')
                ->andReturn($connection = m::mock('Illuminate\Database\Connection'));
        $model->getConnection()
                ->shouldReceive('getQueryGrammar')
                ->andReturn($grammar    = m::mock('Illuminate\Database\Query\Grammars\Grammar'));

        $grammar->shouldReceive('wrap')->with(m::type('String'))->andReturn(m::type('String'));

        $model->getConnection()
                ->shouldReceive('raw')
                ->andReturn($expression = m::mock('Illuminate\Database\Query\Expression'));

        $model->getConnection()
                ->shouldReceive('getPostProcessor')
                ->andReturn($processor = m::mock('Illuminate\Database\Query\Processors\Processor'));


        $queryBuilder = new Builder($model->getConnection(), $grammar, $processor);
        $model->getConnection()
                ->shouldReceive('table')
                ->andReturn($queryBuilder);

        $model->getConnection()
                ->shouldReceive('getTablePrefix')
                ->andReturn('');

        $model->getConnection()
                ->shouldReceive('getDriverName')
                ->andReturn('mysql');

        $grammar->shouldReceive('compileInsertGetId')
                ->andReturn('');
        $grammar->shouldReceive('compileSelect')->once()->andReturn('SELECT * FROM `tbl_widgets_params` WHERE brand_id=? and uid=? and resource=?');
        $connection->shouldReceive('select')
                ->once()
                ->withAnyArgs()
                ->andReturn(null);
        $processor->shouldReceive('processInsertGetId')->andReturn(1);
        $processor->shouldReceive('processSelect')->once()->andReturn([]);

        $this->app['datatables']  = app('yajra\Datatables\Datatables');
        $foundation->shouldReceive('make')->with("antares.brand")->andReturn($model);
        $this->app['antares.app'] = $foundation;
        $this->app['antares.acl'] = $acl;
        $this->app['view']->addNamespace('antares/brands', realpath(base_path() . '../../../../components/brands/resources/views'));
    }

    /**
     * test constructing
     */
    public function testConstruct()
    {

        $mock      = m::mock('\Antares\Contracts\Html\Form\Factory');
        $container = new Container();
        $memory    = m::mock('Antares\Memory\MemoryManager');

        $stub = new Brand($mock, $memory, $container);
        $this->assertSame(get_class($stub), 'Antares\Brands\Http\Presenters\Brand');
    }

    /**
     * testing table method
     */
    public function testTable()
    {

//        $builder = m::mock('\yajra\Datatables\Html\Builder');
//        $builder->shouldReceive('addColumn')->with(m::type('Array'))->andReturnSelf();
//        $builder->shouldReceive('addAction')->with(m::type('Array'))->andReturnSelf();
//        $builder->shouldReceive('setDeferedData')->with(m::type('Array'), 0)->andReturnSelf();
//
//        $this->assertInstanceOf(View::class, $this->stub->table($builder));
    }

    /**
     * test tableJson method
     */
    public function testTableJson()
    {
        $mock        = m::mock('\Antares\Contracts\Html\Form\Factory');
        $container   = new Container();
        $memory      = m::mock('Antares\Memory\MemoryManager');
        $stub        = new Brand($mock, $memory, $container);
        $model       = m::mock('\Illuminate\Database\Eloquent\Model');
        $customField = new Eloquent();
        $model->shouldReceive('select')->with(m::type('Array'))->andReturn($customField->query()->getQuery());
        $this->assertInstanceOf(JsonResponse::class, $stub->tableJson($model));
    }

    /**
     * testing form method
     */
    public function testForm()
    {
        $mock = m::mock('\Antares\Contracts\Html\Form\Factory');
        $mock->shouldReceive('of')
                ->with(m::type('String'), m::type('Closure'))
                ->andReturn(true)
                ->shouldReceive('build')
                ->andReturn(true);

        $eloquent = m::mock('\Antares\Model\Eloquent');
        $eloquent->shouldReceive('getFlattenValidators')
                ->once()
                ->andReturn(array())
                ->shouldReceive('getAttribute')
                ->once()
                ->with(m::type('String'))
                ->andReturn(array());
        $route    = m::mock('\Illuminate\Routing\Route');
        $route->shouldReceive('getParameter')->withAnyArgs()->andReturn(true);

        $this->assertTrue($this->stub->form($eloquent, 'fooAction', $route));
    }

}

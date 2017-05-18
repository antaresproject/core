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

namespace Antares\Brands\Presenters\TestCase;

use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Antares\Brands\Http\Breadcrumb\Breadcrumb;
use Antares\Brands\Http\Presenters\Brand;
use Antares\Testing\ApplicationTestCase;
use Illuminate\Database\Query\Builder;
use Antares\Html\Form\FormBuilder;
use Antares\Brands\Model\Brands;
use Mockery as m;

class BrandTest extends ApplicationTestCase
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
        $breadcrumb                           = m::mock(Breadcrumb::class);
        $breadcrumb->shouldReceive('onBrandEdit')
                ->andReturnNull();
        $this->stub                           = new Brand($breadcrumb);
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
        $connection->shouldReceive('select')->once()->withAnyArgs()->andReturn(null)
                ->shouldReceive('getName')->andReturn('mysql');
        $processor->shouldReceive('processInsertGetId')->andReturn(1);
        $processor->shouldReceive('processSelect')->once()->andReturn([]);


        $foundation->shouldReceive('make')->with("antares.brand")->andReturn($model)
                ->shouldReceive('handles')->andReturn('#');
        $this->app['antares.app'] = $foundation;
        $this->app['antares.acl'] = $acl;
        $this->app['view']->addNamespace('antares/brands', realpath(base_path() . '../../../../components/brands/resources/views'));
    }

    /**
     * test constructing
     */
    public function testConstruct()
    {
        $breadcrumb = m::mock(Breadcrumb::class);

        $stub = new Brand($breadcrumb);
        $this->assertSame(get_class($stub), 'Antares\Brands\Http\Presenters\Brand');
    }

    /**
     * testing form method
     */
    public function testForm()
    {
        $this->app['antares.brand'] = Brands::where('default', 1);
        $this->assertInstanceOf(FormBuilder::class, $this->stub->form(new Brands()));
    }

}

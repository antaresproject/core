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
use Antares\Html\Form\FormBuilder;
use Antares\Brands\Model\Brands;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery as m;

class BrandTest extends ApplicationTestCase
{

    use EloquentConnectionTrait;
    use DatabaseTransactions;

    /**
     * @var \Antares\Brands\Http\Presenters\Brand
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

        $foundation->shouldReceive('make')->with("antares.brand")->andReturn($model)
                ->shouldReceive('handles')->andReturn('#');
        $this->app['antares.app'] = $foundation;
        $this->app['antares.acl'] = $acl;
        $this->app['view']->addNamespace('antares/brands', realpath(base_path() . '../../../../components/brands/resources/views'));
    }

    public function tearDown()
    {
        parent::tearDown();

        m::close();
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
        $this->app['antares.brand'] = Brands::query()->where('default', 1)->first();
        $this->assertInstanceOf(FormBuilder::class, $this->stub->form(new Brands()));
    }

}

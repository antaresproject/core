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


namespace Antares\Brands\TestCase;

use Antares\Brands\Contracts\BrandsRepositoryContract;
use Antares\Brands\Model\Brands as BrandModel;
use Illuminate\Support\Facades\DB;
use Antares\Brands\BrandsTeller;
use Antares\Testbench\TestCase;
use Mockery as m;

class BrandsTellerTest extends TestCase
{

    /**
     * @var BrandsTeller
     */
    private $stub;

    /**
     * @overwrite 
     * @see parent
     */
    public function setUp()
    {
        parent::setUp();
        
        $repository = m::mock(BrandsRepositoryContract::class)
                ->shouldReceive('setDefaultBrandById')
                ->andReturnNull()
                ->shouldReceive('findDefault')
                ->andReturn(new BrandModel)
                ->getMock();
        
        $this->stub = new BrandsTeller($this->app, $repository);
    }

    /**
     * tear down
     */
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * Create a new instance of Brandstelling.
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('Antares\Brands\BrandsTeller', $this->stub);
    }

    /**
     * saving default brand
     * @test
     */
    public function testDefaultBrandById()
    {
        $this->app['antares.app'] = m::mock('\Antares\Contracts\Foundation\Foundation');
        DB::shouldReceive('transaction')
                ->once()
                ->with(m::type('Closure'))
                ->andReturn(m::mock('Illuminate\Database\Query\Builder'));

        $this->assertInstanceOf('Antares\Brands\BrandsTeller', $this->stub->setDefaultBrandById(1));
        $this->assertInstanceOf('Antares\Brands\BrandsTeller', $this->stub->setDefaultBrandById());
    }

    /**
     * gets default brand id
     * @test
     */
    public function testGetDefaultBrandId()
    {

        $model = m::mock('Antares\Brands\Model\Brands');
        $model->shouldReceive('setAttribute')->withAnyArgs()->andReturn($model)
                ->shouldReceive('getAttribute')->with(m::type('String'))->andReturn(1);

        $model->shouldReceive('setConnectionResolver')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('getConnection')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('defaultBrand')->withNoArgs()->andReturn($model);

        $resolver = m::mock('\Illuminate\Database\ConnectionResolverInterface');
        $model->setConnectionResolver($resolver);

        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $foundation->shouldReceive('make')
                ->with('antares.brand')
                ->andReturn($model);

        $this->app['antares.app'] = $foundation;
        $this->assertTrue(is_numeric($this->stub->getDefaultBrandId()));
    }

}

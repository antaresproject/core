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


namespace Antares\Brands\TestCase;

use Antares\Extension\ExtensionServiceProvider;
use Mockery as m;
use Antares\Testbench\TestCase;
use Antares\Brands\Model\Brands as Model;
use Antares\Support\Traits\Testing\EloquentConnectionTrait;

class BrandsTest extends TestCase
{

    use EloquentConnectionTrait;

    /**
     * @var Antares\Brands\Model\Brands
     */
    private $model;

    /**
     * @see parent::setUp
     */
    public function setUp()
    {
        parent::setUp();

        $app           = $this->app;
        $app['events'] = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $app['files']  = m::mock('\Illuminate\Filesystem\Filesystem');

        $extensionStub = new ExtensionServiceProvider($app);
        $extensionStub->register();

        $model       = new Model();
        $this->addMockConnection($model);
        $this->model = $model;
    }

    /**
     * test getDefault method
     */
    public function testGetDefault()
    {
//        $stub                                              = $this->model;
//        $this->app['Illuminate\Database\Eloquent\Builder'] = m::mock('Illuminate\Database\Eloquent\Builder');
//        $this->assertInstanceOf('Antares\Brands\Model\Brands', $stub::defaultBrand());
    }

    /**
     * has timestamps
     */
    public function testHasTimestamps()
    {
        $this->assertTrue($this->model->timestamps);
    }

    /**
     * has valid morph class
     */
    public function testHasValidMorhClass()
    {
        $this->assertSame($this->model->getMorphClass(), 'Antares\Brands\Model\Brands');
    }

    /**
     * has valid table name
     */
    public function testHasValidTableName()
    {
        $this->assertSame('tbl_brands', $this->model->getTable());
    }

    /**
     * test permissions method
     */
    public function testPermissions()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $this->model->permissions());
        $this->assertInstanceOf('\Antares\Model\Permission', $this->model->permissions()->getModel());
    }

    /**
     * test ScopeLatestBy method
     */
    public function testScopeLatestBy()
    {
        $builder = m::mock('\Illuminate\Database\Query\Builder');
        $builder->shouldReceive('orderBy')->andReturnSelf();
        $this->assertNull($this->model->scopeLatestBy($builder));
    }

    /**
     * test ScopeLatest method
     */
    public function testScopeLatest()
    {
        $builder = m::mock('\Illuminate\Database\Query\Builder');
        $builder->shouldReceive('latestBy')->andReturnSelf();
        $this->assertNull($this->model->scopeLatest($builder));
    }

}

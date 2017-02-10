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

use Mockery as m;
use Antares\Brands\Validation\Brand as BrandValidator;
use Antares\Testbench\TestCase;

class BrandTest extends TestCase
{

    /**
     * @var BrandValidator
     */
    private $stub;

    /**
     * @see parent::setUp
     */
    public function setUp()
    {
        parent::setUp();
        $dispatcher = m::mock('\Illuminate\Events\Dispatcher');
        $factory    = m::mock('\Illuminate\Contracts\Validation\Factory');
        $factory->shouldReceive('make')->andReturnSelf();
        $this->stub = new BrandValidator($factory, $dispatcher);
    }

    /**
     * test validateNameOnCreate
     */
    public function testOnCreate()
    {
        $this->assertInstanceOf('Antares\Brands\Validation\Brand', $this->stub->on('create', ['name' => 'foo']));
    }

    /**
     * test has valid rules method
     */
    public function testHasValidRules()
    {
        $this->assertEquals($this->stub->getValidationRules(), ['name' => ['required']]);
    }

    /**
     * test has valid rules method
     */
    public function testHasValidEvents()
    {
        $this->assertEquals($this->stub->getValidationEvents(), ['antares.validate: brands']);
    }

}

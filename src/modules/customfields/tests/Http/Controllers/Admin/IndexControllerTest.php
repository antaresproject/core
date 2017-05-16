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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Customfields\TestCase;

use Antares\Customfields\Http\Controllers\Admin\IndexController;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class IndexControllerTest extends ApplicationTestCase
{

    use WithoutMiddleware;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->addProvider(AutomationServiceProvider::class);
        parent::setUp();
        $this->disableMiddlewareForAllTests();
    }

    /**
     * Test creating instance of class
     * 
     * @test
     */
    public function testConstructWithProcessor()
    {
        $processor = $this->app->make(\Antares\Customfields\Processor\FieldProcessor::class);
        $stub      = new IndexController($processor);
        $this->assertSame(get_class($stub), 'Antares\Customfields\Http\Controllers\Admin\IndexController');
    }

    /**
     * Test setup middleware
     * 
     * @test
     */
    public function testSetupMiddleware()
    {
        $processor = $this->app->make(\Antares\Customfields\Processor\FieldProcessor::class);
        $stub      = new IndexController($processor);
        $this->assertnull($stub->setupMiddleware());
    }

}

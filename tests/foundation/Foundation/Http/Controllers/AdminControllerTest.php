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


namespace Antares\Foundation\Http\Controllers\TestCase;

use Mockery as m;
use Antares\Foundation\Http\Controllers\AdminController;

class AdminControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Http\Controllers\AdminController filters.
     *
     * @test
     */
    public function testFilters()
    {
        $stub = new StubAdminController();

        $beforeFilter = [
            [
                'original'   => 'antares.installable',
                'filter'     => 'antares.installable',
                'parameters' => [],
                'options'    => [],
            ],
        ];

        $this->assertEquals($beforeFilter, $stub->getBeforeFilters());
    }

}

class StubAdminController extends AdminController
{

    protected function setupFilters()
    {
        
    }

    public function setupMiddleware()
    {
        ;
    }

}

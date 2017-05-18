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

use Antares\Testbench\TestCase;
use Antares\Brands\Http\Controllers\Admin\IndexController;
use Mockery as m;

class IndexControllerTest extends TestCase
{

    /**
     * test creating instance of class
     */
    public function testConstructWithProcessor()
    {
        $mock                           = m::mock('Antares\Brands\Processor\Brand');
        $this->app['antares.installed'] = true;
        $stub                           = new IndexController($mock);
        $this->assertSame(get_class($stub), 'Antares\Brands\Http\Controllers\Admin\IndexController');
    }

}

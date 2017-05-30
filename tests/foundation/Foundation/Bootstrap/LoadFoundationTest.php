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

namespace Antares\Foundation\Bootstrap\TestCase;

use Mockery as m;
use Illuminate\Foundation\Application;
use Antares\Foundation\Bootstrap\LoadFoundation;

class LoadFoundationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Foundation\Bootstrap\NotifyIfSafeMode::bootstrap()
     * method.
     *
     * @test
     */
    public function testBootstrapMethod()
    {
        $app = new Application(__DIR__);

        $app['antares.app'] = $foundation         = m::mock('\Antares\Contracts\Foundation\Foundation');

        $foundation->shouldReceive('boot')->once()->andReturnNull();

        (new LoadFoundation())->bootstrap($app);
    }

}

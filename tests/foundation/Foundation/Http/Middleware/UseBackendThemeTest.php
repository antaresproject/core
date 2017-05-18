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

namespace Antares\Foundation\Http\Middleware\TestCase;

use Antares\Foundation\Http\Middleware\UseBackendTheme;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;

class UseBackendThemeTest extends ApplicationTestCase
{

    /**
     * Test Antares\Foundation\Middleware\UseBackendTheme::handle()
     * method.
     *
     * @test
     */
    public function testHandleMethod()
    {
        $events  = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $request = m::mock('\Illuminate\Http\Request');

        $events->shouldReceive('fire')->with('antares.started: admin')->andReturnNull()
                ->shouldReceive('fire')->with('antares.ready: admin')->andReturnNull()
                ->shouldReceive('fire')->with('antares.ready: menu')->andReturnNull()
                ->shouldReceive('fire')->with('antares.done: admin')->andReturnNull();

        $next = function ($request) {
            return 'foo';
        };

        $stub = new UseBackendTheme($events);

        $this->assertEquals('foo', $stub->handle($request, $next));
    }

}

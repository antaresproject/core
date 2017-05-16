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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Http\Handlers\Tests;

use Antares\Tester\Http\Handlers\TesterMenu as Stub;
use Antares\Contracts\Auth\Guard;
use Antares\Auth\SessionGuard;
use Antares\Testing\TestCase;
use Mockery as m;

class TesterMenuHandlerTest extends TestCase
{

    /**
     * Check whether the menu should be displayed.
     * 
     * @test
     */
    public function testAuthorize()
    {
        $this->app['antares.acl'] = $acl                      = m::mock(SessionGuard::class);
        $acl->shouldReceive('make')->with('antares/tester')->once()->andReturnSelf()
                ->shouldReceive('can')->with('tools-tester')->once()->andReturn(true);
        $stub                     = new Stub($this->app);
        $guardMock                = m::mock(Guard::class);
        $guardMock->shouldReceive('guest')->andReturn(false);

        $this->assertTrue($stub->authorize($guardMock));
    }

    /**
     * Create a handler.
     *
     * @test
     */
    public function testHandle()
    {
        $stub                    = new Stub($this->app);
        $this->app[Guard::class] = $guard                   = m::mock(Guard::class);
        $guard->shouldReceive('guest')->andReturn(false);
        $this->assertNull($stub->handle());
    }

}

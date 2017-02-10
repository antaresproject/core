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
 namespace Antares\Foundation\Http\Filters\TestCase;

use Mockery as m;
use Antares\Foundation\Http\Filters\IsInstalled;

class IsInstalledTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Filters\IsInstalled::filter()
     * method when installed.
     *
     * @test
     */
    public function testFilterMethodWhenInstalled()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $auth       = m::mock('\Illuminate\Contracts\Auth\Guard');
        $config     = m::mock('\Illuminate\Contracts\Config\Repository');

        $foundation->shouldReceive('installed')->once()->andReturn(true)
            ->shouldReceive('handles')->once()->with('antares::login')->andReturn('http://localhost/admin/login');
        $auth->shouldReceive('guest')->once()->andReturn(true);
        $config->shouldReceive('get')->once()->with('antares/foundation::routes.guest')->andReturn('antares::login');

        $stub = new IsInstalled($foundation, $auth, $config);

        $this->assertInstanceOf('\Illuminate\Http\RedirectResponse', $stub->filter());
    }

    /**
     * Test Antares\Foundation\Filters\IsInstalled::filter()
     * method when not installed.
     *
     * @test
     */
    public function testFilterMethodWhenNotInstalled()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $auth       = m::mock('\Illuminate\Contracts\Auth\Guard');
        $config     = m::mock('\Illuminate\Contracts\Config\Repository');

        $foundation->shouldReceive('installed')->once()->andReturn(false);

        $stub = new IsInstalled($foundation, $auth, $config);

        $this->assertNull($stub->filter());
    }
}

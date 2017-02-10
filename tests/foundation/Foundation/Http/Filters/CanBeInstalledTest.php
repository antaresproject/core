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
use Antares\Foundation\Http\Filters\CanBeInstalled;

class CanBeInstalledTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Foundation\Filters\CanBeInstalled::filter()
     * method can be installed.
     *
     * @test
     */
    public function testFilterMethodCanBeInstalled()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');

        $foundation->shouldReceive('installed')->once()->andReturn(false)
            ->shouldReceive('handles')->once()->with('antares::install')->andReturn('http://localhost/admin/install');

        $stub = new CanBeInstalled($foundation);

        $this->assertInstanceOf('\Illuminate\Http\RedirectResponse', $stub->filter());
    }

    /**
     * Test Antares\Foundation\Filters\CanBeInstalled::filter()
     * method can't be installed.
     *
     * @test
     */
    public function testFilterMethodCantBeInstalled()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');

        $foundation->shouldReceive('installed')->once()->andReturn(true);

        $stub = new CanBeInstalled($foundation);

        $this->assertNull($stub->filter());
    }
}

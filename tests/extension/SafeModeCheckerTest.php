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
 namespace Antares\Extension\TestCase;

use Mockery as m;
use Antares\Extension\SafeModeChecker;

class SafeModeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Extension\Debugger::check() method when safe mode is
     * "on".
     *
     * @test
     */
    public function testCheckMethodWhenSafeModeIsOn()
    {
        $request = m::mock('\Illuminate\Http\Request');
        $config  = m::mock('\Illuminate\Contracts\Config\Repository');

        $stub = new SafeModeChecker($config, $request);

        $request->shouldReceive('input')->once()->with('_mode', 'safe')->andReturn('safe');
        $config->shouldReceive('get')->once()->with('antares/extension::mode', 'normal')->andReturn('safe')
            ->shouldReceive('set')->once()->with('antares/extension::mode', 'safe')->andReturn(null);

        $this->assertTrue($stub->check());
    }

    /**
     * Test Antares\Extension\Debugger::check() method when safe mode is
     * "off".
     *
     * @test
     */
    public function testCheckMethodWhenSafeModeIsOff()
    {
        $request = m::mock('\Illuminate\Http\Request');
        $config  = m::mock('\Illuminate\Contracts\Config\Repository');

        $stub = new SafeModeChecker($config, $request);

        $request->shouldReceive('input')->once()->with('_mode', 'normal')->andReturn(null);
        $config->shouldReceive('get')->once()->with('antares/extension::mode', 'normal')->andReturn('normal')
            ->shouldReceive('set')->once()->with('antares/extension::mode', 'normal')->andReturn(null);

        $this->assertFalse($stub->check());
    }
}

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
use Antares\Foundation\Http\Filters\IsRegistrable;

class IsRegistrableTest extends \PHPUnit_Framework_TestCase
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
     * method can be registered.
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testFilterMethodCanBeRegistered()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $memory     = m::mock('\Antares\Contracts\Memory\Provider');

        $foundation->shouldReceive('memory')->once()->andReturn($memory);
        $memory->shouldReceive('get')->once()->with('site.registrable', false)->andReturn(false);

        $stub = new IsRegistrable($foundation);

        $stub->filter();
    }

    /**
     * Test Antares\Foundation\Filters\CanBeInstalled::filter()
     * method can't be registered.
     *
     * @test
     */
    public function testFilterMethodCantBeRegistered()
    {
        $foundation = m::mock('\Antares\Contracts\Foundation\Foundation');
        $memory     = m::mock('\Antares\Contracts\Memory\Provider');

        $foundation->shouldReceive('memory')->once()->andReturn($memory);
        $memory->shouldReceive('get')->once()->with('site.registrable', false)->andReturn(true);

        $stub = new IsRegistrable($foundation);

        $this->assertNull($stub->filter());
    }
}

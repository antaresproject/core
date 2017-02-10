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
 namespace Antares\Resources\Routing\TestCase;

use Mockery as m;
use Antares\Resources\Routing\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Antares\Resources\Routing\Route::overrideParameters() method.
     *
     * @test
     */
    public function testOverrideParametersMethod()
    {
        $stub = new Route('GET', 'laravel/framework', ['uses' => 'FooController']);

        $refl       = new \ReflectionObject($stub);
        $parameters = $refl->getProperty('parameters');
        $parameters->setAccessible(true);

        $this->assertNull($parameters->getValue($stub));

        $expected = ['foo' => 'bar'];

        $stub->overrideParameters($expected);

        $this->assertEquals($expected, $parameters->getValue($stub));
    }
}

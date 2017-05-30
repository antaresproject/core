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

namespace Antares\Memory\Handlers\TestCase;

use Mockery as m;
use Antares\Memory\Handlers\Cache;

class CacheTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Memory\Handlers\Cache::initiate() method.
     *
     * @test
     */
    public function testInitiateMethod()
    {
        $cache = m::mock('\Illuminate\Contracts\Cache\Repository');

        $value = [
            'name'  => 'Antares',
            'theme' => [
                'backend'  => 'default',
                'frontend' => 'default',
            ],
        ];

        $cache->shouldReceive('get')->once()->andReturn($value);

        $stub = new Cache('cachemock', [], $cache);

        $this->assertEquals($value, $stub->initiate());
    }

    /**
     * Test Antares\Memory\Handlers\Cache::finish().
     *
     * @test
     */
    public function testFinishMethod()
    {
        $cache = m::mock('\Illuminate\Contracts\Cache\Repository');

        $cache->shouldReceive('forever')->once()->andReturn(true);

        $stub = new Cache('cachemock', [], $cache);

        $this->assertTrue($stub->finish());
    }

}

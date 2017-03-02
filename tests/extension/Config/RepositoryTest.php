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

namespace Antares\Extension\Config\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Extension\Config\Repository;

class RepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Extension\Config\Repository::map() method.
     *
     * @test
     */
    public function testMapMethod()
    {
        $app     = new Container();
        $manager = m::mock('\Antares\Memory\MemoryManager', [$app]);
        $memory  = m::mock('\Antares\Contracts\Memory\Provider');
        $config  = m::mock('\Illuminate\Contracts\Config\Repository');

        $manager->shouldReceive('make')->once()->andReturn($memory);
        $memory->shouldReceive('get')->once()
                ->with('extension_laravel/framework', [])
                ->andReturn(['foobar' => 'foobar is awesome'])
                ->shouldReceive('put')->once()
                ->with('extension_laravel/framework', ['foobar' => 'foobar is awesome', 'foo' => 'foobar'])
                ->andReturn(true);
        $config->shouldReceive('set')->once()
                ->with('laravel/framework::foobar', 'foobar is awesome')
                ->andReturn(true)
                ->shouldReceive('get')->once()
                ->with('laravel/framework::foobar')->andReturn('foobar is awesome')
                ->shouldReceive('get')->once()
                ->with('laravel/framework::foo')->andReturn('foobar');

        $stub = new Repository($config, $manager);

        $stub->map('laravel/framework', [
            'foo'    => 'laravel/framework::foo',
            'foobar' => 'laravel/framework::foobar',
        ]);
    }

}

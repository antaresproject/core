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

namespace Antares\Asset\TestCase;

use Antares\Asset\AssetSymlinker;
use Antares\Extension\Manager;
use Mockery as m;
use Antares\Asset\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test contructing Antares\Asset\Factory.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $dispatcher = m::mock('\Antares\Asset\Dispatcher');
        $extensionsManager = m::mock(Manager::class);
        $assetSymlinker = m::mock(AssetSymlinker::class);

        $dispatcher->shouldReceive('addVersioning')->once()->andReturn(null)->shouldReceive('removeVersioning')->once()->andReturn(null);

        $env  = new Factory($dispatcher, $extensionsManager, $assetSymlinker);
        $stub = $env->container();

        $this->assertInstanceOf('\Antares\Asset\Asset', $stub);

        $env->addVersioning()->removeVersioning();
    }

    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

}

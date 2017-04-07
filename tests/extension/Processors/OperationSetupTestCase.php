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

use Antares\Console\Kernel;
use Antares\Extension\Contracts\Config\SettingsContract;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Composer\Package\CompletePackageInterface;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Mockery as m;

abstract class OperationSetupTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Mockery\MockInterface
     */
    protected $container;

    /**
     * @var \Mockery\MockInterface
     */
    protected $dispatcher;

    /**
     * @var \Mockery\MockInterface
     */
    protected $kernel;

    public function setUp() {
        parent::setUp();

        $this->container    = m::mock(Container::class);
        $this->dispatcher   = m::mock(Dispatcher::class);
        $this->kernel       = m::mock(Kernel::class);
    }

    public function tearDown() {
        parent::tearDown();
        m::close();
    }

    /**
     * @param $name
     * @return \Mockery\MockInterface
     */
    protected function buildExtensionMock($name) {
        $package = m::mock(CompletePackageInterface::class)
            ->shouldReceive('getName')
            ->andReturn($name)
            ->getMock();

        $settings = m::mock(SettingsContract::class)
            ->shouldReceive('getData')
            ->andReturn([])
            ->getMock();

        return m::mock(ExtensionContract::class)
            ->shouldReceive('getPackage')
            ->andReturn($package)
            ->getMock()
            ->shouldReceive('getSettings')
            ->andReturn($settings)
            ->getMock();
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function buildOperationHandlerMock() {
        return m::mock(OperationHandlerContract::class);
    }

}
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

namespace Antares\Extension\TestCase;

use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Symfony\Component\Process\Process;
use Antares\Extension\Processors\Composer;
use Antares\Extension\Composer\Handler as ComposerHandler;
use Illuminate\Events\Dispatcher;
use Mockery as m;
use Antares\Testbench\ApplicationTestCase;

class ComposerTest extends ApplicationTestCase
{

    /**
     * @var Mockery
     */
    protected $dispatcher;

    /**
     * @var Mockery
     */
    protected $composerHandler;

    public function setUp()
    {
        parent::setUp();

        $this->dispatcher      = m::mock(Dispatcher::class);
        $this->composerHandler = m::mock(ComposerHandler::class);
    }

    /**
     * @return Composer
     */
    protected function getProcessor()
    {
        return new Composer($this->composerHandler, $this->dispatcher);
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function buildOperationHandlerMock()
    {
        return m::mock(OperationHandlerContract::class);
    }

    public function testWithoutExtensions()
    {
        $handler = $this->buildOperationHandlerMock()
                ->shouldReceive('operationInfo')
                ->once()
                ->andReturnNull()
                ->getMock();

        $this->app['log']  = m::mock(\Psr\Log\LoggerInterface::class)->shouldReceive('error')->once()->withAnyArgs()->getMock();

        $this->getProcessor()->run($handler, []);
    }

    public function testAsSuccess()
    {
        $handler = $this->buildOperationHandlerMock()
                ->shouldReceive('operationInfo')
                ->andReturnNull()
                ->getMock();

        $extensions = [
            'antaresproject/component-aaa',
            'antaresproject/component-bbb:1.2',
        ];

        //$expectedCommand = 'composer require antaresproject/component-aaa antaresproject/component-bbb:1.2 --no-progress';

        $process = m::mock(Process::class)
                ->shouldReceive('stop')
                ->andReturnNull()
                ->getMock()
                ->shouldReceive('isSuccessful')
                ->andReturn(true)
                ->getMock();

        $this->composerHandler->shouldReceive('run')
                ->withAnyArgs()
                ->andReturn($process)
                ->getMock();

        $this->dispatcher->shouldReceive('fire')->once()->andReturnNull()->getMock();

        $this->getProcessor()->run($handler, $extensions);
    }

}

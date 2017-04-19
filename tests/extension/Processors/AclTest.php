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

use Antares\Acl\Migration;
use Antares\Acl\RoleActionList;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Processors\Acl;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;
use Antares\Testbench\ApplicationTestCase;

class AclTest extends ApplicationTestCase
{

    use ExtensionMockTrait;

    /**
     * @var Mockery
     */
    protected $aclMigration;

    public function setUp() {
        parent::setUp();

        $this->aclMigration = m::mock(Migration::class);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @return Acl
     */
    protected function getProcessor() {
        return new Acl($this->aclMigration);
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function buildOperationHandlerMock() {
        return m::mock(OperationHandlerContract::class);
    }

    public function testWithoutFile() {
        $extension = $this->buildExtensionMock('aaa')
            ->shouldReceive('getPath')
            ->once()
            ->andReturn(m::type('string'))
            ->getMock();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationInfo')
            ->once()
            ->withAnyArgs()
            ->andReturnNull()
            ->getMock();

        $file = m::mock(Filesystem::class)
            ->shouldReceive('getRequire')
            ->withAnyArgs()
            ->once()
            ->andThrow(FileNotFoundException::class)
            ->getMock();

        $this->app->instance('files', $file);

        $this->getProcessor()->import($handler, $extension);

    }

    public function testWithoutValidObject() {
        $extension = $this->buildExtensionMock('aaa')
            ->shouldReceive('getPath')
            ->once()
            ->andReturn(m::type('string'))
            ->getMock();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationInfo')
            ->never()
            ->getMock();

        $file = m::mock(Filesystem::class)
            ->shouldReceive('getRequire')
            ->withAnyArgs()
            ->once()
            ->andReturnNull()
            ->getMock();

        $this->app->instance('files', $file);

        $this->getProcessor()->import($handler, $extension);
    }

    public function testException() {
        $extension = $this->buildExtensionMock('aaa')
            ->shouldReceive('getPath')
            ->once()
            ->andReturn(m::type('string'))
            ->getMock();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationFailed')
            ->once()
            ->andReturnNull()
            ->getMock();

        $file = m::mock(Filesystem::class)
            ->shouldReceive('getRequire')
            ->withAnyArgs()
            ->once()
            ->andThrow(\Exception::class)
            ->getMock();

        $this->app->instance('files', $file);

        $this->getProcessor()->import($handler, $extension);
    }

    public function testWithoutReload() {
        $extension = $this->buildExtensionMock('aaa')
            ->shouldReceive('getPath')
            ->once()
            ->andReturn(m::type('string'))
            ->getMock();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationInfo')
            ->once()
            ->andReturnNull()
            ->shouldReceive('operationSuccess')
            ->once()
            ->andReturnNull()
            ->getMock();

        $roleActionList = m::mock(RoleActionList::class);

        $file = m::mock(Filesystem::class)
            ->shouldReceive('getRequire')
            ->withAnyArgs()
            ->once()
            ->andReturn($roleActionList)
            ->getMock();

        $this->aclMigration
            ->shouldReceive('up')
            ->once()
            ->with('aaa', $roleActionList)
            ->andReturnNull()
            ->getMock();

        $this->app->instance('files', $file);

        $this->getProcessor()->import($handler, $extension);
    }

    public function testWithReload() {
        $extension = $this->buildExtensionMock('aaa')
            ->shouldReceive('getPath')
            ->once()
            ->andReturn(m::type('string'))
            ->getMock();

        $handler = $this->buildOperationHandlerMock()
            ->shouldReceive('operationInfo')
            ->twice()
            ->andReturnNull()
            ->shouldReceive('operationSuccess')
            ->once()
            ->andReturnNull()
            ->getMock();

        $roleActionList = m::mock(RoleActionList::class);

        $file = m::mock(Filesystem::class)
            ->shouldReceive('getRequire')
            ->withAnyArgs()
            ->once()
            ->andReturn($roleActionList)
            ->getMock();

        $this->aclMigration
            ->shouldReceive('down')
            ->once()
            ->with('aaa')
            ->andReturnNull()
            ->getMock()
            ->shouldReceive('up')
            ->once()
            ->with('aaa', $roleActionList)
            ->andReturnNull()
            ->getMock();

        $this->app->instance('files', $file);

        $this->getProcessor()->import($handler, $extension, true);
    }

}
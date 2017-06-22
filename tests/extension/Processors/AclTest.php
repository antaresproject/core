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

use Antares\Acl\Migration;
use Antares\Acl\RoleActionList;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Processors\Acl;
use Illuminate\Filesystem\Filesystem;
use Mockery as m;
use Antares\Testbench\ApplicationTestCase;
use Composer\Package\CompletePackageInterface;
use Antares\Extension\Contracts\Config\SettingsContract;
use Antares\Extension\Contracts\ExtensionContract;

class AclTest extends ApplicationTestCase
{

    /**
     * @var Mockery
     */
    protected $aclMigration;

    public function setUp()
    {
        parent::setUp();

        $this->aclMigration = m::mock(Migration::class);
    }

    /**
     * @param $name
     * @return \Mockery\MockInterface
     */
    protected function buildExtensionMock($name)
    {
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
     * @return Acl
     */
    protected function getProcessor()
    {
        return new Acl($this->aclMigration);
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function buildOperationHandlerMock()
    {
        return m::mock(OperationHandlerContract::class);
    }

    public function testWithoutFile()
    {
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
                ->shouldReceive('exists')
                ->with(m::type('string'))
                ->once()
                ->andReturn(false)
                ->getMock();

        $this->app->instance('files', $file);

        $this->getProcessor()->import($handler, $extension);
    }

    public function testWithoutValidObject()
    {
        $extension = $this->buildExtensionMock('aaa')
                ->shouldReceive('getPath')
                ->once()
                ->andReturn(m::type('string'))
                ->getMock();

        $handler = $this->buildOperationHandlerMock()
                ->shouldReceive('operationFailed')
                ->once()
                ->getMock();

        $file = m::mock(Filesystem::class)
                ->shouldReceive('exists')
                ->with(m::type('string'))
                ->once()
                ->andReturn(true)
                ->getMock()
                ->shouldReceive('getRequire')
                ->withAnyArgs()
                ->once()
                ->andReturnNull()
                ->getMock();

        $this->app->instance('files', $file);

        $this->getProcessor()->import($handler, $extension);
    }

    public function testException()
    {
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
                ->shouldReceive('exists')
                ->with(m::type('string'))
                ->once()
                ->andReturn(true)
                ->getMock()
                ->shouldReceive('getRequire')
                ->withAnyArgs()
                ->once()
                ->andThrow(\Exception::class)
                ->getMock();

        $this->app['log'] = m::mock(\Psr\Log\LoggerInterface::class)->shouldReceive('error')->once()->withAnyArgs()->getMock();

        $this->app->instance('files', $file);

        $this->getProcessor()->import($handler, $extension);
    }

    public function testWithoutReload()
    {
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
                ->shouldReceive('exists')
                ->with(m::type('string'))
                ->once()
                ->andReturn(true)
                ->getMock()
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

    public function testWithReload()
    {
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
                ->shouldReceive('exists')
                ->with(m::type('string'))
                ->once()
                ->andReturn(true)
                ->getMock()
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

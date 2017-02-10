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
 namespace Antares\Installation\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Installation\Requirement;

class RequirementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Application instance.
     *
     * @var Illuminate\Foundation\Application
     */
    private $app = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->app       = new Container();
        $this->app['db'] = m::mock('\Illuminate\Database\DatabaseManager');
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        unset($this->app);

        m::close();
    }

    /**
     * Test construct Antares\Foundation\Installation\Requirement.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $app         = $this->app;
        $stub        = new Requirement($app);
        $refl        = new \ReflectionObject($stub);
        $checklist   = $refl->getProperty('checklist');
        $installable = $refl->getProperty('installable');

        $checklist->setAccessible(true);
        $installable->setAccessible(true);

        $checklist->setValue($stub, ['foo', 'bar']);
        $installable->setValue($stub, true);

        $this->assertEquals(['foo', 'bar'], $stub->getChecklist());
        $this->assertTrue($stub->isInstallable());
    }

    /**
     * Test Antares\Foundation\Installation\Requirement::check() method.
     *
     * @test
     */
    public function testCheckMethod()
    {
        $app  = $this->app;
        $stub = m::mock('\Antares\Installation\Requirement[checkDatabaseConnection,checkWritableStorage,checkWritableAsset]', [$app]);
        $stub->shouldReceive('checkDatabaseConnection')
                ->once()->andReturn(['is' => true, 'explicit' => true, 'should' => true])
            ->shouldReceive('checkWritableStorage')
                ->once()->andReturn(['is' => false, 'explicit' => true, 'should' => true])
            ->shouldReceive('checkWritableAsset')
                ->once()->andReturn(['is' => true, 'explicit' => true, 'should' => true]);

        $this->assertFalse($stub->check());
        $this->assertFalse($stub->isInstallable());
    }

    /**
     * Test Antares\Foundation\Installation\Foundation::checkDatabaseConnection()
     * with valid database connection.
     *
     * @test
     */
    public function testCheckDatabaseConnectionWithValidConnection()
    {
        $this->app['db']->shouldReceive('connection')
                ->once()->andReturn($this->app['db'])
            ->shouldReceive('getPdo')
                ->once()->andReturn(true);

        $stub   = new Requirement($this->app);
        $result = $stub->checkDatabaseConnection();

        $this->assertTrue($result['is']);
        $this->assertTrue($result['explicit']);
    }

    /**
     * Test Antares\Foundation\Installation\Foundation::checkDatabaseConnection()
     * with invalid database connection.
     *
     * @test
     */
    public function testCheckDatabaseConnectionWithInvalidConnection()
    {
        $this->app['db']->shouldReceive('connection')
                ->once()->andReturn($this->app['db'])
            ->shouldReceive('getPdo')
                ->once()->andThrow('PDOException');

        $stub   = new Requirement($this->app);
        $result = $stub->checkDatabaseConnection();

        $this->assertFalse($result['is']);
        $this->assertTrue($result['explicit']);
    }

    /**
     * Test Antares\Foundation\Installation\Requirement::checkWritableStorage()
     * method.
     *
     * @test
     */
    public function testCheckWritableStorageMethod()
    {
        $app                 = $this->app;
        $app['path.storage'] = '/foo/storage/';
        $app['html']         = $html         = m::mock('\Antares\Html\HtmlBuilder[create]');
        $app['files']        = $file        = m::mock('\Illuminate\Filesystem\Filesystem[isWritable]');

        $html->shouldReceive('create')
            ->with('code', 'storage', ['title' => '/foo/storage/'])->once()->andReturn('');
        $file->shouldReceive('isWritable')->with('/foo/storage/')->once()->andReturn(true);

        $stub = new Requirement($app);

        $result = $stub->checkWritableStorage();

        $this->assertTrue($result['is']);
        $this->assertTrue($result['explicit']);
    }

    /**
     * Test Antares\Foundation\Installation\Requirement::checkWritableAsset()
     * method.
     *
     * @test
     */
    public function testCheckWritableAssetMethod()
    {
        $app                = $this->app;
        $app['path.public'] = '/foo/public/';
        $app['html']        = $html        = m::mock('\Antares\Html\HtmlBuilder[create]');
        $app['files']       = $file       = m::mock('\Illuminate\Filesystem\Filesystem[isWritable]');

        $html->shouldReceive('create')->once()
            ->with('code', 'public/packages', m::any())->andReturn('');
        $file->shouldReceive('isWritable')->with('/foo/public/packages/')->once()->andReturn(true);

        $stub = new Requirement($app);

        $result = $stub->checkWritableAsset();

        $this->assertTrue($result['is']);
        $this->assertFalse($result['explicit']);
    }
}

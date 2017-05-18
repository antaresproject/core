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

namespace Antares\Config\TestCase;

use Mockery as m;
use Antares\Config\FileLoader;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{

    public function testEmptyArrayIsReturnedOnNullPath()
    {
        $loader = $this->getLoader();
        $this->assertEquals([], $loader->load('local', 'group', 'namespace'));
    }

    public function testBasicArrayIsReturned()
    {
        $loader = $this->getLoader();
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__ . '/app.php')->andReturn(true);
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__ . '/local/app.php')->andReturn(false);
        $loader->getFilesystem()->shouldReceive('getRequire')->once()->with(__DIR__ . '/app.php')->andReturn(['foo' => 'bar']);
        $array  = $loader->load('local', 'app', null);

        $this->assertEquals(['foo' => 'bar'], $array);
    }

    public function testEnvironmentArrayIsMerged()
    {
        $loader = $this->getLoader();
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__ . '/app.php')->andReturn(true);
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__ . '/local/app.php')->andReturn(true);
        $loader->getFilesystem()->shouldReceive('getRequire')->once()->with(__DIR__ . '/app.php')->andReturn(['foo' => 'bar', 'providers' => ['AppProvider']]);
        $loader->getFilesystem()->shouldReceive('getRequire')->once()->with(__DIR__ . '/local/app.php')->andReturn(['foo' => 'blah', 'baz' => 'boom', 'providers' => [1 => 'SomeProvider']]);
        $array  = $loader->load('local', 'app', null);

        $this->assertEquals(['foo' => 'blah', 'baz' => 'boom', 'providers' => ['AppProvider', 'SomeProvider']], $array);
    }

    public function testGroupExistsReturnsTrueWhenTheGroupExists()
    {
        $loader = $this->getLoader();
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__ . '/app.php')->andReturn(true);
        $this->assertTrue($loader->exists('app'));
    }

    public function testGroupExistsReturnsTrueWhenNamespaceGroupExists()
    {
        $loader = $this->getLoader();
        $loader->addNamespace('namespace', __DIR__ . '/namespace');
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__ . '/namespace/app.php')->andReturn(true);
        $this->assertTrue($loader->exists('app', 'namespace'));
    }

    public function testGroupExistsReturnsFalseWhenNamespaceHintDoesntExists()
    {
        $loader = $this->getLoader();
        $this->assertFalse($loader->exists('app', 'namespace'));
    }

    public function testGroupExistsReturnsFalseWhenNamespaceGroupDoesntExists()
    {
        $loader = $this->getLoader();
        $loader->addNamespace('namespace', __DIR__ . '/namespace');
        $loader->getFilesystem()->shouldReceive('exists')->with(__DIR__ . '/namespace/app.php')->andReturn(false);
        $this->assertFalse($loader->exists('app', 'namespace'));
    }

    public function testCascadingPackagesProperlyLoadsFiles()
    {
        $loader = $this->getLoader();
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__ . '/packages/dayle/rees/group.php')->andReturn(true);
        $loader->getFilesystem()->shouldReceive('getRequire')->once()->with(__DIR__ . '/packages/dayle/rees/group.php')->andReturn(['bar' => 'baz']);
        $loader->getFilesystem()->shouldReceive('exists')->once()->with(__DIR__ . '/packages/dayle/rees/local/group.php')->andReturn(true);
        $loader->getFilesystem()->shouldReceive('getRequire')->once()->with(__DIR__ . '/packages/dayle/rees/local/group.php')->andReturn(['foo' => 'boom']);
        $items  = $loader->cascadePackage('local', 'dayle/rees', 'group', ['foo' => 'bar']);

        $this->assertEquals(['foo' => 'boom', 'bar' => 'baz'], $items);
    }

    protected function getLoader()
    {
        return new FileLoader(m::mock('\Illuminate\Filesystem\Filesystem'), __DIR__);
    }

}

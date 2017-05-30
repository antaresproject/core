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

namespace Antares\Translation\Tests;

use Mockery as m;
use Antares\Translation\FileLoader;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Illuminate\Translation\FileLoader.
     *
     * @test
     */
    public function testFileLoaderInstance()
    {
        $files = m::mock('Illuminate\Filesystem\Filesystem');
        $stub  = new FileLoader($files, '/var/app/langs');

        $this->assertInstanceOf('Illuminate\Translation\FileLoader', $stub);
        $this->assertInstanceOf('Illuminate\Translation\LoaderInterface', $stub);
    }

    /**
     * Test Illuminate\Translation\FileLoader::loadNamespaced() method.
     *
     * @test
     */
    public function testLoadNamespacedMethod()
    {
        $path  = '/var/app/lang';
        $files = m::mock('Illuminate\Filesystem\Filesystem');
        $stub  = new FileLoader($files, $path);

        $stub->addNamespace('antares/foundation', '/var/vendor/antares/foundation/src/lang');

        $files->shouldReceive('exists')->once()
                ->with("/var/vendor/antares/foundation/src/lang/en/title.php")->andReturn(true)
                ->shouldReceive('getRequire')->once()
                ->with("/var/vendor/antares/foundation/src/lang/en/title.php")->andReturn(['home' => 'Home', 'install' => 'Install'])
                ->shouldReceive('exists')->once()
                ->with("{$path}/vendor/en/antares/foundation/title.php")->andReturn(true)
                ->shouldReceive('getRequire')->once()
                ->with("{$path}/vendor/en/antares/foundation/title.php")->andReturn(['install' => 'Installation'])
                ->shouldReceive('exists')->once()
                ->with("{$path}/packages/antares/foundation/en/title.php")->andReturn(true)
                ->shouldReceive('getRequire')->once()
                ->with("{$path}/packages/antares/foundation/en/title.php")->andReturn(['home' => 'Home Page', 'install' => 'Installed']);

        $this->assertEquals(
                ['home' => 'Home Page', 'install' => 'Installation'], $stub->load('en', 'title', 'antares/foundation')
        );
    }

}

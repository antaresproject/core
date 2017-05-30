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
use Antares\Testbench\ApplicationTestCase;
use Antares\View\FileViewFinder;
use Mockery as m;

class FileViewFinderTest extends ApplicationTestCase
{

    /**
     * Filesystem instance.
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    private $files = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->files = m::mock('\Illuminate\Filesystem\Filesystem');
    }

    /**
     * Test Antares\View\FileViewFinder::findNamedPathView() method.
     *
     * @test
     */
    public function testFindNamedPathViewMethod()
    {
        $files = $this->files;
        $files->shouldReceive('exists')->once()->with("/path/vendor/foo/bar/views/hello.php")->andReturn(true)
                ->shouldReceive('exists')->with("/path/vendor/foo/bar/views/hello.php")->andReturn(false)
                ->shouldReceive('exists')->with("/path/app/views/packages/foo/bar/hello.php;")->andReturn(false);

        $stub = new FileViewFinder($files, ['/path/theme/views', '/path/app/views'], ['php']);

        $stub->addNamespace('foo/bar', '/path/vendor/foo/bar/views');
        $this->assertEquals('/path/vendor/foo/bar/views/hello.php', $stub->find("foo/bar::hello"));
    }

    /**
     * Test Antares\View\FileViewFinder::setPaths() method.
     *
     * @test
     */
    public function testSetPathsMethod()
    {
        $files = $this->files;
        $stub  = new FileViewFinder($files, ['/path/theme/views', '/path/app/views'], ['php']);

        $refl  = new \ReflectionObject($stub);
        $paths = $refl->getProperty('paths');
        $paths->setAccessible(true);

        $expected = ['/path/antares/views'];
        $stub->setPaths($expected);

        $this->assertEquals($expected, $paths->getValue($stub));
    }

}

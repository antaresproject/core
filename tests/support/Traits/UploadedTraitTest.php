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

namespace Antares\Support\Traits\TestCase;

use Illuminate\Support\Facades\File;
use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedTraitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $app = new Container();

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($app);
    }

    /**
     * Test Antares\Support\Traits\UploadableTrait::saveUploadedFile() method.
     *
     * @test
     */
    public function testSaveUploadedFileMethod()
    {
        $path = '/var/www/public/';
        $file = m::mock('\Symfony\Component\HttpFoundation\File\UploadedFile[getClientOriginalExtension,move]', [
                    realpath(__DIR__ . '/fixtures') . '/test.gif',
                    'test',
        ]);

        $file->shouldReceive('getClientOriginalExtension')->once()->andReturn('jpg')
                ->shouldReceive('move')->once()->with($path, m::type('String'))->andReturnNull();

        $stub = new UploadedStub();

        $filename = $stub->save($file, $path);
    }

    /**
     * Test Antares\Support\Traits\UploadableTrait::saveUploadedFile() method
     * when custom getUploadedFilename() are available.
     *
     * @test
     */
    public function testSaveUploadedFileMethodWithCustomFilename()
    {
        $path = '/var/www/public/';
        $file = m::mock('\Symfony\Component\HttpFoundation\File\UploadedFile[move]', [
                    realpath(__DIR__ . '/fixtures') . '/test.gif',
                    'test',
        ]);

        $file->shouldReceive('move')->once()->with($path, 'foo.jpg')->andReturnNull();

        $stub = new UploadedStubWithReplacement();

        $filename = $stub->save($file, $path);
    }

    /**
     * Test Antares\Support\Traits\UploadableTrait::deleteUploadedFile() method.
     *
     * @test
     */
    public function testDeleteMethod()
    {
        $filesystem = m::mock('\Illuminate\Filesystem\Filesystem');
        $filename   = '/var/www/foo.jpg';

        $filesystem->shouldReceive('delete')->once()->with($filename)->andReturn(true);

        File::swap($filesystem);

        $stub = new UploadedStub();

        $this->assertTrue($stub->delete($filename));
    }

}

class UploadedStub
{

    use \Antares\Support\Traits\UploadableTrait;

    public function save(UploadedFile $file, $path)
    {
        return $this->saveUploadedFile($file, $path);
    }

    public function delete($file)
    {
        return $this->deleteUploadedFile($file);
    }

}

class UploadedStubWithReplacement
{

    use \Antares\Support\Traits\UploadableTrait;

    public function save(UploadedFile $file, $path)
    {
        return $this->saveUploadedFile($file, $path);
    }

    public function delete($file)
    {
        return $this->deleteUploadedFile($file);
    }

    protected function getUploadedFilename(UploadedFile $file)
    {
        return 'foo.jpg';
    }

}

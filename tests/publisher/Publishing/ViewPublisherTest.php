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
 namespace Antares\Publisher\Publishing\TestCase;

use Mockery as m;
use Antares\Publisher\Publishing\ViewPublisher;

class ViewPublisherTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testPackageViewPublishing()
    {
        $pub = new ViewPublisher($files = m::mock('\Illuminate\Filesystem\Filesystem'), __DIR__);
        $pub->setPackagePath(__DIR__.'/vendor');
        $files->shouldReceive('isDirectory')->once()->with(__DIR__.'/vendor/foo/bar/resources/views')->andReturn(true);
        $files->shouldReceive('isDirectory')->once()->with(__DIR__.'/packages/foo/bar')->andReturn(true);
        $files->shouldReceive('copyDirectory')->once()->with(__DIR__.'/vendor/foo/bar/resources/views', __DIR__.'/packages/foo/bar')->andReturn(true);

        $this->assertTrue($pub->publishPackage('foo/bar'));

        $pub = new ViewPublisher($files2 = m::mock('\Illuminate\Filesystem\Filesystem'), __DIR__);
        $files2->shouldReceive('isDirectory')->once()->with(__DIR__.'/custom-packages/foo/bar/resources/views')->andReturn(false)
            ->shouldReceive('isDirectory')->once()->with(__DIR__.'/custom-packages/foo/bar/views')->andReturn(true);
        $files2->shouldReceive('isDirectory')->once()->with(__DIR__.'/packages/foo/bar')->andReturn(true);
        $files2->shouldReceive('copyDirectory')->once()->with(__DIR__.'/custom-packages/foo/bar/views', __DIR__.'/packages/foo/bar')->andReturn(true);

        $this->assertTrue($pub->publishPackage('foo/bar', __DIR__.'/custom-packages'));
    }
}

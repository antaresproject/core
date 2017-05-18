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

namespace Antares\View\TestCase\Theme;

use Mockery as m;
use Antares\View\Theme\Manifest;

class ManifestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\View\Theme\Manifest.
     *
     * @test
     */
    public function testManifest()
    {
        $files = m::mock('\Illuminate\Filesystem\Filesystem');

        $files->shouldReceive('exists')->once()->with('/var/antares/themes/default/theme.json')->andReturn(true)
                ->shouldReceive('get')->once()->with('/var/antares/themes/default/theme.json')->andReturn('{"name":"foobar"}');

        $stub = new Manifest($files, '/var/antares/themes/default');

        $this->assertNull($stub->foobar);
        $this->assertEquals('foobar', $stub->name);
        $this->assertEquals('foobar', $stub->get('name'));
        $this->assertFalse(isset($stub->hello));
        $this->assertTrue(is_array($stub->autoload));

        $this->assertEquals('/var/antares/themes/default', $stub->path);

        $this->assertInstanceOf('\Illuminate\Support\Fluent', $stub->items());
        $this->assertEquals('foobar', $stub->items()->get('name'));
    }

    /**
     * Test Antares\View\Theme\Manifest throws an exception.
     *
     * @expectedException \RuntimeException
     */
    public function testManifestThrowsException()
    {
        $files = m::mock('\Illuminate\Filesystem\Filesystem');

        $files->shouldReceive('exists')->once()->with('/var/antares/themes/default/theme.json')->andReturn(true)
                ->shouldReceive('get')->once()->with('/var/antares/themes/default/theme.json')->andReturn('{"foo}');

        new Manifest($files, '/var/antares/themes/default');
    }

}

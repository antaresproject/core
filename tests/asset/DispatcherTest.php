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

namespace Antares\Asset\TestCase;

use Mockery as m;
use Antares\Asset\Dispatcher;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Orchesta\Asset\Dispatcher::run() method.
     *
     * @test
     */
    public function testRunMethod()
    {
        $files    = m::mock('\Illuminate\Filesystem\Filesystem');
        $html     = m::mock('\Antares\Html\HtmlBuilder');
        $resolver = m::mock('\Antares\Asset\DependencyResolver');
        $path     = '/var/public';

        $script = [
            'jquery' => [
                'source'       => '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js',
                'dependencies' => [],
                'attributes'   => [],
            ],
            'foo'    => [
                'source'       => 'foo.js',
                'dependencies' => [],
                'attributes'   => [],
            ],
            'foobar' => null,
        ];

        $assets = [
            'script' => $script,
            'style'  => [],
        ];

        $files->shouldReceive('lastModified')->once()->andReturn('');
        $html->shouldReceive('script')->twice()
                ->with('foo.js', m::any())
                ->andReturn('foo')
                ->shouldReceive('script')->twice()
                ->with('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', m::any())
                ->andReturn('jquery');
        $resolver->shouldReceive('arrange')->twice()->with($script)->andReturn($script);

        $stub = new Dispatcher($files, $html, $resolver, $path);

        $stub->addVersioning();

        //$this->assertEquals('jqueryfoo', $stub->run('script', $assets));
        $this->assertEquals('', $stub->run('style', $assets));

        $stub->removeVersioning();

        //$this->assertEquals('jqueryfoo', $stub->run('script', $assets));
    }

    /**
     * Test Orchesta\Asset\Dispatcher::run() method using remote path.
     *
     * @test
     */
    public function testRunMethodUsingRemotePath()
    {
        $files    = m::mock('\Illuminate\Filesystem\Filesystem');
        $html     = m::mock('\Antares\Html\HtmlBuilder');
        $resolver = m::mock('\Antares\Asset\DependencyResolver');
        $path     = '//cdn.foobar.com';

        $script = [
            'jquery' => [
                'source'       => '//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js',
                'dependencies' => [],
                'attributes'   => [],
            ],
            'foo'    => [
                'source'       => 'foo.js',
                'dependencies' => [],
                'attributes'   => [],
            ],
            'foobar' => null,
        ];

        $assets = [
            'script' => $script,
            'style'  => [],
        ];

        $html->shouldReceive('script')->twice()
                ->with('//cdn.foobar.com/foo.js', m::any())
                ->andReturn('foo')
                ->shouldReceive('script')->twice()
                ->with('//cdn.foobar.com/ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js', m::any())
                ->andReturn('jquery');
        $resolver->shouldReceive('arrange')->twice()->with($script)->andReturn($script);

        $stub = new Dispatcher($files, $html, $resolver, $path);

        $stub->addVersioning();

        $this->assertEquals('jqueryfoo', $stub->run('script', $assets));
        $this->assertEquals('', $stub->run('style', $assets));

        $stub->removeVersioning();

        $this->assertEquals('jqueryfoo', $stub->run('script', $assets));
    }

}

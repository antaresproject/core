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
use Antares\Asset\Asset;

class AssetTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test constructing Antares\Asset\Asset.
     *
     * @test
     */
    public function testConstructMethod()
    {
        $dispatcher = m::mock('\Antares\Asset\Dispatcher');

        $assets = [
            'script' => [
                'foo' => [
                    'source'       => 'foo.js',
                    'dependencies' => [],
                    'attributes'   => [],
                    'replaces'     => [],
                ],
            ],
            'style'  => [
                '*'      => [
                    'source'       => 'all.min.css',
                    'dependencies' => [],
                    'attributes'   => ['media' => 'all'],
                    'replaces'     => ['foobar', 'foo', 'hello'],
                ],
                'foobar' => [
                    'source'       => 'foobar.css',
                    'dependencies' => [],
                    'attributes'   => ['media' => 'all'],
                    'replaces'     => [],
                ],
                'foo'    => [
                    'source'       => 'foo.css',
                    'dependencies' => ['foobar'],
                    'attributes'   => ['media' => 'all'],
                    'replaces'     => [],
                ],
                'hello'  => [
                    'source'       => 'hello.css',
                    'dependencies' => ['jquery'],
                    'attributes'   => ['media' => 'all'],
                    'replaces'     => [],
                ],
            ],
        ];

        $dispatcher->shouldReceive('run')->twice()->with('script', $assets, null)->andReturn('scripted')
                ->shouldReceive('run')->twice()->with('style', $assets, null)->andReturn('styled')
                ->shouldReceive('run')->twice()->with('inline', $assets, null)->andReturn('inlined');

        $stub = new Asset('default', $dispatcher);

        $stub->add('foo', 'foo.js');
        $stub->add('foobar', 'foobar.css');
        $stub->style('foo', 'foo.css', ['foobar']);
        $stub->style('hello', 'hello.css', ['jquery']);
        $stub->style(['foobar', 'foo', 'hello'], 'all.min.css');
        $this->assertEquals('scriptedinlined', $stub->scripts());
        $this->assertEquals('styled', $stub->styles());
        $this->assertEquals('scriptedstyledinlined', $stub->show());
    }

    /**
     * Test Antares\Asset\Asset::prefix() method.
     *
     * @test
     */
    public function testPrefixMethod()
    {
        $dispatcher = m::mock('\Antares\Asset\Dispatcher');

        $prefix = '//ajax.googleapis.com/ajax/libs/';
        $assets = [];

        $dispatcher->shouldReceive('run')->once()->with('script', $assets, $prefix)->andReturn('scripted')
                ->shouldReceive('run')->once()->with('style', $assets, $prefix)->andReturn('styled')
                ->shouldReceive('run')->once()->with('inline', $assets, $prefix)->andReturn('inlined');

        $stub = new Asset('default', $dispatcher);
        $stub->prefix($prefix);

        $this->assertEquals('scriptedstyledinlined', $stub->show());
    }

    /**
     * Test Antares\Asset\Asset::asset() method return empty string
     * when name is not defined.
     *
     * @test
     */
    public function testAssetMethod()
    {
        $dispatcher = m::mock('\Antares\Asset\Dispatcher');

        $dispatcher->shouldReceive('run')->once()->with('script', [], null)->andReturn('')
                ->shouldReceive('run')->once()->with('inline', [], null)->andReturn('');


        $stub = new Asset('default', $dispatcher);
        $this->assertEquals('', $stub->scripts());
    }

}

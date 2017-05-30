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

use Antares\Asset\DependencyResolver;

class DependencyResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Antares\Asset\DependencyResolver::arrange() method.
     *
     * @test
     */
    public function testArrangeMethod()
    {
        $stub = new DependencyResolver();

        $output = [
            'app' => [
                'source'       => 'app.min.js',
                'dependencies' => ['jquery', 'bootstrap', 'backbone'],
                'attributes'   => [],
                'replaces'     => [],
            ],
            'jquery-ui' => [
                'source'       => 'jquery.ui.min.js',
                'dependencies' => ['jquery'],
                'attributes'   => [],
                'replaces'     => [],
            ],
            'jquery' => [
                'source'       => 'jquery.min.js',
                'dependencies' => [],
                'attributes'   => [],
                'replaces'     => [],
            ],
            'bootstrap' => [
                'source'       => 'bootstrap.min.js',
                'dependencies' => ['jquery'],
                'attributes'   => [],
                'replaces'     => [],
            ],
            'backbone' => [
                'source'       => 'backbone.min.js',
                'dependencies' => ['jquery', 'zepto'],
                'attributes'   => [],
                'replaces'     => [],
            ],
            'jquery.min' => [
                'source'       => 'all.min.js',
                'dependencies' => [],
                'attributes'   => [],
                'replaces'     => ['jquery', 'jquery-ui'],
            ],
        ];

        $expected = [
            'jquery.min' => [
                'source'       => 'all.min.js',
                'dependencies' => [],
                'attributes'   => [],
                'replaces'     => [],
            ],
            'bootstrap' => [
                'source'       => 'bootstrap.min.js',
                'dependencies' => [],
                'attributes'   => [],
                'replaces'     => [],
            ],
            'backbone' => [
                'source'       => 'backbone.min.js',
                'dependencies' => [],
                'attributes'   => [],
                'replaces'     => [],
            ],
            'app' => [
                'source'       => 'app.min.js',
                'dependencies' => [],
                'attributes'   => [],
                'replaces'     => [],
            ],
        ];

        $this->assertEquals($expected, $stub->arrange($output));
    }

    /**
     * Test Antares\Asset\DependencyResolver::arrange() method throws
     * exception given self dependence.
     *
     * @expectedException \RuntimeException
     */
    public function testArrangeMethodThrowsExceptionGivenSelfDependence()
    {
        $stub = new DependencyResolver();

        $output = [
            'jquery-ui' => [
                'source'       => 'jquery.ui.min.js',
                'dependencies' => ['jquery-ui'],
                'attributes'   => [],
                'replaces'     => [],
            ],
        ];

        $stub->arrange($output);
    }

    /**
     * Test Antares\Asset\DependencyResolver::arrange() method throws
     * exception given circular dependence.
     *
     * @expectedException \RuntimeException
     */
    public function testArrangeMethodThrowsExceptionGivenCircularDependence()
    {
        $stub = new DependencyResolver();

        $output = [
            'jquery-ui' => [
                'source'       => 'jquery.ui.min.js',
                'dependencies' => ['jquery'],
                'attributes'   => [],
                'replaces'     => [],
            ],
            'jquery' => [
                'source'       => 'jquery.min.js',
                'dependencies' => ['jquery-ui'],
                'attributes'   => [],
                'replaces'     => [],
            ],
        ];

        $stub->arrange($output);
    }
}

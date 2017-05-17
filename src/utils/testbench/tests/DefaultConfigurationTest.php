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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Testbench\TestCase;

class DefaultConfigurationTest extends \Antares\Testbench\TestCase
{
    /**
     * `cache.default` value is set to array.
     *
     * @test
     */
    public function testDefaultCacheConfiguration()
    {
        $this->assertEquals('array', $this->app['config']['cache.default']);
    }

    /**
     * `session.driver` value is set to array.
     *
     * @test
     */
    public function testDefaultSessionConfiguration()
    {
        $this->assertEquals('array', $this->app['config']['session.driver']);
    }
}

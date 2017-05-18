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
 namespace Antares\Support\Providers\TestCase;

use Antares\Support\Providers\ServiceProvider;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Antares\Support\Providers\PipelineServiceProvider method signature.
     *
     * @test
     */
    public function testInstanceSignature()
    {
        $stub = new StubBasicProvider(null);

        $this->assertContains('Antares\Support\Providers\Traits\PackageProviderTrait', class_uses_recursive(get_class($stub)));
    }
}

class StubBasicProvider extends ServiceProvider
{
    public function register()
    {
            }
}

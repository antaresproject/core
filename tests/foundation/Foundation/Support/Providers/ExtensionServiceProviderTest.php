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

namespace Antares\Foundation\Support\Providers\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Antares\Foundation\Support\Providers\ExtensionServiceProvider;

class ExtensionServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Foundation\Providers\ExtensionServiceProvider
     * is deferred.
     *
     * @test
     */
    public function testServiceProviderIsDeferred()
    {
        $stub = new ExtensionServiceProvider(null);

        $this->assertTrue($stub->isDeferred());
    }

    /**
     * Test Antares\Foundation\Providers\ExtensionServiceProvider::register()
     * method.
     *
     * @test
     */
    public function testRegisterMethod()
    {
        $stub = new ExtensionServiceProvider(null);

        $this->assertNull($stub->register());
    }

    /**
     * Test Antares\Foundation\Providers\ExtensionServiceProvider::boot()
     * method.
     *
     * @test
     */
    public function testBootMethod()
    {
        $app                             = new Container();
        $app['antares.extension.finder'] = $finder                          = m::mock('\Antares\Contracts\Extension\Finder');

        $finder->shouldReceive('addPath')->once()->with('app::Extensions/*/*')->andReturnNull()
                ->shouldReceive('registerExtension')->once()->with('forum', 'base::modules/forum')->andReturnNull();

        $stub = new StubExtensionProvider($app);

        $this->assertNull($stub->boot());
    }

    /**
     * Test Antares\Foundation\Providers\ExtensionServiceProvider::when()
     * method.
     *
     * @test
     */
    public function testWhenIsProvided()
    {
        $stub = new ExtensionServiceProvider(null);

        $this->assertContains('antares.extension: detecting', $stub->when());
    }

}

class StubExtensionProvider extends ExtensionServiceProvider
{

    protected $extensions = [
        'app::Extensions/*/*',
        'forum' => 'base::modules/forum',
    ];

}

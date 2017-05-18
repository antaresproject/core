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

namespace Antares\Foundation\TestCase;

use Antares\Foundation\Providers\ConsoleSupportServiceProvider;
use Antares\Foundation\Providers\FoundationServiceProvider;
use Antares\Foundation\Providers\ArtisanServiceProvider;
use Antares\Foundation\Providers\SupportServiceProvider;
use Antares\Testing\TestCase;

class ServiceProviderTest extends TestCase
{

    /**
     * Test instance of `antares.publisher`.
     *
     * @test
     */
    public function testInstanceOfAntaresPublisher()
    {
        $stub = $this->app->make('antares.publisher');
        $this->assertInstanceOf('\Antares\Foundation\Publisher\PublisherManager', $stub);
    }

    /**
     * Test instance of eloquents.
     *
     * @test
     */
    public function testInstanceOfEloquents()
    {
        $stub = $this->app->make('antares.role');
        $this->assertInstanceOf('\Antares\Model\Role', $stub);

        $stub = $this->app->make('antares.user');
        $this->assertInstanceOf('\Antares\Model\User', $stub);
    }

    /**
     * Test list of provides.
     *
     * @test
     */
    public function testListOfProvides()
    {
        $foundation = new FoundationServiceProvider($this->app);
        $site       = new SupportServiceProvider($this->app);
        $console    = new ConsoleSupportServiceProvider($this->app);
        $artisan    = new ArtisanServiceProvider($this->app);

        $this->assertEquals($this->getFoundationProvides(), $foundation->provides());
        $this->assertFalse($foundation->isDeferred());

        $this->assertEquals($this->getSupportProvides(), $site->provides());
        $this->assertTrue($site->isDeferred());
        $this->assertNotEmpty($console->provides());
        $this->assertTrue($console->isDeferred());

        $this->assertInstanceOf('\Antares\Config\Console\ConfigCacheCommand', $this->app['command.config.cache']);
        $this->assertTrue($artisan->isDeferred());
    }

    /**
     * Get value of Antares\Foundation\Providers\FoundationServiceProvider::provides().
     *
     * @return array
     */
    protected function getFoundationProvides()
    {
        return [
            'antares.app',
            'antares.installed',
            'antares.meta',
        ];
    }

    /**
     * Get value of Antares\Foundation\Providers\SupportServiceProvider::provides().
     *
     * @return array.
     */
    protected function getSupportProvides()
    {
        return [
            'antares.publisher',
            'antares.role',
            'antares.user',
        ];
    }

}

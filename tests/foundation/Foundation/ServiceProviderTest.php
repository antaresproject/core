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

namespace Antares\Foundation\TestCase;

use Mockery as m;
use Antares\Testing\TestCase;
use Antares\Foundation\Providers\ArtisanServiceProvider;
use Antares\Foundation\Providers\SupportServiceProvider;
use Antares\Foundation\Providers\FoundationServiceProvider;
use Antares\Foundation\Providers\ConsoleSupportServiceProvider;

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

        $this->assertEquals($this->getConsoleSupportProvides(), $console->provides());
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

    /**
     * Get value of Antares\Foundation\Providers\ConsoleSupportServiceProvider::provides().
     *
     * @return array
     */
    protected function getConsoleSupportProvides()
    {
        return [
            0  => "command.clear-compiled",
            1  => "command.auth.resets.clear",
            2  => "command.config.cache",
            3  => "command.config.clear",
            4  => "command.down",
            5  => "command.environment",
            6  => "command.key.generate",
            7  => "command.optimize",
            8  => "command.route.cache",
            9  => "command.route.clear",
            10 => "command.route.list",
            11 => "command.tinker",
            12 => "command.up",
            13 => "command.view.clear",
            14 => "command.app.name",
            15 => "command.auth.make",
            16 => "command.cache.table",
            17 => "command.console.make",
            18 => "command.controller.make",
            19 => "command.event.generate",
            20 => "command.event.make",
            21 => "command.job.make",
            22 => "command.listener.make",
            23 => "command.middleware.make",
            24 => "command.model.make",
            25 => "command.policy.make",
            26 => "command.provider.make",
            27 => "command.queue.failed-table",
            28 => "command.queue.table",
            29 => "command.request.make",
            30 => "command.seeder.make",
            31 => "command.session.table",
            32 => "command.serve",
            33 => "command.test.make",
            34 => "command.vendor.publish",
            35 => "Illuminate\Console\Scheduling\ScheduleRunCommand",
            36 => "migrator",
            37 => "migration.repository",
            38 => "command.migrate",
            39 => "command.migrate.rollback",
            40 => "command.migrate.reset",
            41 => "command.migrate.refresh",
            42 => "command.migrate.install",
            43 => "command.migrate.status",
            44 => "migration.creator",
            45 => "command.migrate.make",
            46 => "seeder",
            47 => "command.seed",
            48 => "composer",
            49 => "command.queue.failed",
            50 => "command.queue.retry",
            51 => "command.queue.forget",
            52 => "command.queue.flush",
            53 => "antares.commands.auth",
            54 => "antares.commands.extension.activate",
            55 => "antares.commands.extension.deactivate",
            56 => "antares.commands.extension.detect",
            57 => "antares.commands.extension.migrate",
            58 => "antares.commands.extension.publish",
            59 => "antares.commands.extension.refresh",
            60 => "antares.commands.extension.reset",
            61 => "antares.commands.extension.composer",
            62 => "antares.commands.extension.composer-dumpautoload",
            63 => "antares.commands.memory",
            64 => "antares.commands.assemble",
            65 => "antares.commands.queue",
            66 => "command.asset.publish",
            67 => "command.config.publish",
            68 => "command.view.publish",
            69 => "asset.publisher",
            70 => "config.publisher",
            71 => "view.publisher",
            72 => "antares.view.command.detect",
            73 => "antares.view.command.optimize"
        ];
    }

}

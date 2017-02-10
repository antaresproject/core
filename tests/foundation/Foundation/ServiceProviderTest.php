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
            'command.auth.resets.clear',
            'Illuminate\Console\Scheduling\ScheduleRunCommand',
            'migrator',
            'migration.repository',
            'command.migrate',
            'command.migrate.rollback',
            'command.migrate.reset',
            'command.migrate.refresh',
            'command.migrate.install',
            'command.migrate.status',
            'migration.creator',
            'command.migrate.make',
            'seeder',
            'command.seed',
            'composer',
            'command.queue.table',
            'command.queue.failed',
            'command.queue.retry',
            'command.queue.forget',
            'command.queue.flush',
            'command.queue.failed-table',
            'command.controller.make',
            'command.middleware.make',
            'command.session.database',
            'antares.commands.auth',
            'antares.commands.extension.activate',
            'antares.commands.extension.deactivate',
            'antares.commands.extension.detect',
            'antares.commands.extension.migrate',
            'antares.commands.extension.publish',
            'antares.commands.extension.refresh',
            'antares.commands.extension.reset',
            'antares.commands.memory',
            'antares.commands.optimize',
            'antares.optimize',
            'asset.publisher',
            'command.asset.publish',
            'view.publisher',
            'command.view.publish',
            'antares.view.command.activate',
            'antares.view.command.detect',
            'antares.view.command.optimize',
        ];
    }
}

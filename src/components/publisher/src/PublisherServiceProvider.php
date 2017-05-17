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


namespace Antares\Publisher;

use Illuminate\Support\ServiceProvider;

class PublisherServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMigration();

        $this->registerAssetPublisher();
    }

    /**
     * Register the service provider for Antares migrator.
     *
     * @return void
     */
    protected function registerMigration()
    {
        $this->app->singleton('antares.publisher.migrate', function ($app) {
            $app->make('migration.repository');
            return new MigrateManager($app, $app->make('migrator'), $app->make('Antares\Publisher\Seeder'));
        });
    }

    /**
     * Register the service provider for Antares asset publisher.
     *
     * @return void
     */
    protected function registerAssetPublisher()
    {
        $this->app->singleton('antares.publisher.asset', function ($app) {
            return new AssetManager($app, $app->make('asset.publisher'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'antares.publisher.migrate',
            'antares.publisher.asset',
        ];
    }

}

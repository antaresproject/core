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

use Antares\Publisher\Publishing\ViewPublisher;
use Antares\Publisher\Publishing\AssetPublisher;
use Antares\Publisher\Publishing\ConfigPublisher;
use Antares\Publisher\Console\ViewPublishCommand;
use Antares\Publisher\Console\AssetPublishCommand;
use Antares\Publisher\Console\ConfigPublishCommand;
use Antares\Support\Providers\CommandServiceProvider as ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'AssetPublish'  => 'command.asset.publish',
        'ConfigPublish' => 'command.config.publish',
        'ViewPublish'   => 'command.view.publish',
    ];

    /**
     * Additional provides.
     *
     * @var array
     */
    protected $provides = [
        'asset.publisher',
        'config.publisher',
        'view.publisher',
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAssetPublisher();

        $this->registerConfigPublisher();

        $this->registerViewPublisher();

        parent::register();
    }

    /**
     * Register the asset publisher service and command.
     *
     * @return void
     */
    protected function registerAssetPublisher()
    {
        $this->app->singleton('asset.publisher', function ($app) {
            $publicPath = $app->make('path.public');
            $publisher  = new AssetPublisher($app->make('files'), $publicPath);
            $publisher->setPackagePath($app->make('path.base') . '/src');

            return $publisher;
        });
    }

    /**
     * Register the configuration publisher class and command.
     *
     * @return void
     */
    protected function registerConfigPublisher()
    {
        $this->app->singleton('config.publisher', function ($app) {
            $path = $app->make('path.config');

            $publisher = new ConfigPublisher($app->make('files'), $path);

            $publisher->setPackagePath($app->make('path.base') . '/src');

            return $publisher;
        });
    }

    /**
     * Register the view publisher class and command.
     *
     * @return void
     */
    protected function registerViewPublisher()
    {
        $this->app->singleton('view.publisher', function ($app) {
            $viewPath  = $app->make('path.base') . '/resources/views';
            $publisher = new ViewPublisher($app->make('files'), $viewPath);
            $publisher->setPackagePath($app->make('path.base') . '/src');

            return $publisher;
        });
    }

    /**
     * Register the asset publish console command.
     *
     * @return void
     */
    protected function registerAssetPublishCommand()
    {
        $this->app->singleton('command.asset.publish', function ($app) {
            return new AssetPublishCommand($app->make('asset.publisher'));
        });
    }

    /**
     * Register the configuration publish console command.
     *
     * @return void
     */
    protected function registerConfigPublishCommand()
    {
        $this->app->singleton('command.config.publish', function ($app) {
            return new ConfigPublishCommand($app->make('config.publisher'));
        });
    }

    /**
     * Register the view publish console command.
     *
     * @return void
     */
    protected function registerViewPublishCommand()
    {
        $this->app->singleton('command.view.publish', function ($app) {
            return new ViewPublishCommand($app->make('view.publisher'));
        });
    }

}

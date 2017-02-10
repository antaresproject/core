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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Extension;

use Antares\Extension\Console\ResetCommand;
use Antares\Extension\Console\DetectCommand;
use Antares\Extension\Console\MigrateCommand;
use Antares\Extension\Console\PublishCommand;
use Antares\Extension\Console\RefreshCommand;
use Antares\Extension\Console\ActivateCommand;
use Antares\Extension\Console\DeactivateCommand;
use Antares\Extension\Console\ComposerCommand;
use Antares\Extension\Console\ComposerDumpautoloadCommand;
use Antares\Support\Providers\CommandServiceProvider as ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Activate'             => 'antares.commands.extension.activate',
        'Deactivate'           => 'antares.commands.extension.deactivate',
        'Detect'               => 'antares.commands.extension.detect',
        'Migrate'              => 'antares.commands.extension.migrate',
        'Publish'              => 'antares.commands.extension.publish',
        'Refresh'              => 'antares.commands.extension.refresh',
        'Reset'                => 'antares.commands.extension.reset',
        'Composer'             => 'antares.commands.extension.composer',
        'ComposerDumpautoload' => 'antares.commands.extension.composer-dumpautoload',
    ];

    /**
     * Register composer command
     *
     * @return void
     */
    protected function registerComposerCommand()
    {
        $this->app->singleton('antares.commands.extension.composer', function () {
            return new ComposerCommand();
        });
    }

    /**
     * Register composer command
     *
     * @return void
     */
    protected function registerComposerDumpautoloadCommand()
    {
        $this->app->singleton('antares.commands.extension.composer-dumpautoload', function () {
            return new ComposerDumpautoloadCommand();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerActivateCommand()
    {
        $this->app->singleton('antares.commands.extension.activate', function () {
            return new ActivateCommand();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerDeactivateCommand()
    {
        $this->app->singleton('antares.commands.extension.deactivate', function () {
            return new DeactivateCommand();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerDetectCommand()
    {
        $this->app->singleton('antares.commands.extension.detect', function () {
            return new DetectCommand();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('antares.commands.extension.migrate', function () {
            return new MigrateCommand();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerPublishCommand()
    {
        $this->app->singleton('antares.commands.extension.publish', function () {
            return new PublishCommand();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerRefreshCommand()
    {
        $this->app->singleton('antares.commands.extension.refresh', function () {
            return new RefreshCommand();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerResetCommand()
    {
        $this->app->singleton('antares.commands.extension.reset', function () {
            return new ResetCommand();
        });
    }

}

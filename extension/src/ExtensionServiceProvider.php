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

use Antares\Extension\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Antares\Support\Providers\ServiceProvider;

class ExtensionServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerExtensionConfigManager();

        $this->registerExtensionFinder();

        $this->registerExtensionProvider();

        $this->registerExtensionStatusChecker();

        $this->registerExtension();

        $this->registerExtensionEvents();
    }

    /**
     * Register the service provider for Extension Provider.
     *
     * @return void
     */
    protected function registerExtensionProvider()
    {
        $this->app->singleton('antares.extension.provider', function (Application $app) {
            $provider = new ProviderRepository($app, $app->make('events'), $app->make('files'));
            $provider->loadManifest();
            return $provider;
        });
    }

    /**
     * Register the service provider for Extension Safe Mode Checker.
     *
     * @return void
     */
    protected function registerExtensionStatusChecker()
    {
        $this->app->singleton('antares.extension.status', function (Application $app) {
            return new SafeModeChecker($app->make('config'), $app->make('request'));
        });
        $this->app->singleton('antares.extension.mode', function (Application $app) {
            return new SafeModeChecker($app->make('config'), $app->make('request'));
        });
    }

    /**
     * Register the service provider for Extension.
     *
     * @return void
     */
    protected function registerExtension()
    {
        $this->app->singleton('antares.extension', function ($app) {
            $dispatcher = new Dispatcher(
                    $app->make('config'), $app->make('events'), $app->make('files'), $app->make('antares.extension.finder'), new ProviderRepository($app)
            );
            $status     = $app->make('antares.extension.status');
            return new Factory($app, $dispatcher, $status);
        });
    }

    /**
     * Register the service provider for Extension Config Manager.
     *
     * @return void
     */
    protected function registerExtensionConfigManager()
    {
        $this->app->singleton('antares.extension.config', function ($app) {
            return new Repository($app->make('config'), $app->make('antares.memory'));
        });
    }

    /**
     * Register the service provider for Extension Finder.
     *
     * @return void
     */
    protected function registerExtensionFinder()
    {
        $this->app->singleton('antares.extension.finder', function ($app) {
            $config = array_merge([
                'paths'   => [
                    'app::', 'vendor::', 'base::'
                ],
                'pattern' => 'manifest.json'], [
                'path.app'  => $app->make('path'),
                'path.base' => $app->make('path.base'),
            ]);
            return new Finder($app->make('files'), $config);
        });
    }

    /**
     * Register the service provider for Extension Safe Mode Checker.
     *
     * @return void
     */
    protected function registerExtensionSafeModeChecker()
    {
        $this->app->singleton('antares.extension.mode', function ($app) {
            return new SafeModeChecker($app->make('config'), $app->make('request'));
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/../');

        $this->addConfigComponent('antares/extension', 'antares/extension', $path . '/resources/config');
    }

    /**
     * Register extension events.
     *
     * @return void
     */
    protected function registerExtensionEvents()
    {
        $this->app->terminating(function () {
            $extension = $this->app->make('antares.extension');
            if (!is_null($extension)) {
                $extension->finish();
            }
        });
    }

}

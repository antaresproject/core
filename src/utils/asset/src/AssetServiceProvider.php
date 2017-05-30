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

namespace Antares\Asset;

use Antares\Asset\Http\Middleware\AfterMiddleware;
use Symfony\Component\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class AssetServiceProvider extends ServiceProvider
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
        $this->registerResolver();

        $this->registerDispatcher();

        $this->registerAsset();

        $this->registerAssetSymlinker();

        $this->registerAssetPublisher();
    }

    /**
     * Booting service provider
     */
    public function boot()
    {
        $this->app->make(Router::class)->pushMiddlewareToGroup('web', AfterMiddleware::class);
    }

    /**
     * registering asset symlinker
     */
    protected function registerAssetSymlinker()
    {
        $this->app->singleton('asset.symlinker', function($app) {
            $publicPath = sandbox_path('packages/antares');
            $symlinker  = new AssetSymlinker(new Filesystem, $publicPath);
            return $symlinker;
        });
    }

    /**
     * registering asset publisher
     */
    protected function registerAssetPublisher()
    {
        $this->app->singleton('antares.asset.publisher', function($app) {
            return new AssetPublisher($app->make('antares.asset'), $app->make('asset.symlinker'));
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerAsset()
    {
        $this->app->singleton('antares.asset', function ($app) {
            return new Factory($app->make('antares.asset.dispatcher'));
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerDispatcher()
    {
        $this->app->singleton('antares.asset.dispatcher', function ($app) {
            return new Dispatcher(
                    $app->make('files'), $app->make('html'), $app->make('antares.asset.resolver'), $app->make('path.public')
            );
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerResolver()
    {
        $this->app->singleton('antares.asset.resolver', function () {
            return new DependencyResolver();
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
            'antares.asset', 'antares.asset.dispatcher', 'antares.asset.resolver', 'asset.symlinker', 'antares.asset.publisher'
        ];
    }

}

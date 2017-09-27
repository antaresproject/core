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
        //TODO: probably to remove because of invalid Dumper class.
        //$this->app->make(Router::class)->pushMiddlewareToGroup('web', AfterMiddleware::class);
    }

    /**
     * registering asset symlinker
     */
    protected function registerAssetSymlinker()
    {
//        $this->app->singleton('asset.symlinker', function() {
//            $publicPath = sandbox_path('packages/antares');
//
//            return new AssetSymlinker(new Filesystem, $publicPath);
//        });

        $this->app->singleton(AssetSymlinker::class, function() {
            $publicPath = sandbox_path('packages/antares');

            return new AssetSymlinker(new Filesystem, $publicPath);
        });

        $this->app->alias(AssetSymlinker::class, 'asset.symlinker');
    }

    /**
     * registering asset publisher
     */
    protected function registerAssetPublisher()
    {
        $this->app->singleton(AssetPublisher::class);
        $this->app->alias(AssetPublisher::class, 'antares.asset.publisher');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerAsset()
    {
        $this->app->singleton(Factory::class);
        $this->app->alias(Factory::class, 'antares.asset');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerDispatcher()
    {
        $this->app->singleton(Dispatcher::class, function() {
            return new Dispatcher(
                $this->app->make('files'), $this->app->make('html'), $this->app->make('antares.asset.resolver'), $this->app->make('path.public')
            );
        });

        $this->app->alias(Dispatcher::class, 'antares.asset.dispatcher');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerResolver()
    {
        $this->app->singleton(DependencyResolver::class);
        $this->app->alias(DependencyResolver::class, 'antares.asset.resolver');

//        $this->app->singleton('antares.asset.resolver', function () {
//            return new DependencyResolver();
//        });
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

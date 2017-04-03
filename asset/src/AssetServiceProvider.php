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

namespace Antares\Asset;

use Antares\Asset\Http\Middleware\AfterMiddleware;
use Assetic\Factory\Worker\CacheBustingWorker;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\AsseticExtension;
use Symfony\Component\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Assetic\Cache\FilesystemCache;
use Assetic\Factory\AssetFactory;
use Assetic\Asset\AssetCache;
use Assetic\FilterManager;
use Assetic\AssetManager;
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

        $this->registerAssetic();

        $this->registerAsseticFactory();

        $this->registerAsseticWriter();

        $this->registerAsseticFilterManager();

        $this->registerAsseticManager();

        $this->registerAsseticLazyManager();

        $this->registerAsseticDumper();

        $this->registerAsseticCommand();
    }

    /**
     * Asset Factory configuration happens here
     */
    protected function registerAssetic()
    {
        $this->app['assetic'] = $this->app->share(function ($app) {
            $app['assetic.path_to_web'] = $app['config']->get('asset::config.path_to_web');
            if ($app['config']->has('asset::config.path_to_source')) {
                $app['assetic.path_to_source'] = $app['config']->get('asset::config.path_to_source');
            }
            $app['assetic.options'] = $app['config']->get('asset::config.options');
            // initializing lazy asset manager
            if (isset($app['assetic.formulae']) && !is_array($app['assetic.formulae']) && !empty($app['assetic.formulae'])) {
                $app['assetic.lazy_asset_manager'];
            }
            return $app['assetic.factory'];
        });
    }

    /**
     * Registers assetic factory
     */
    protected function registerAsseticFactory()
    {
        $this->app['assetic.factory'] = $this->app->share(function ($app) {
            $root    = isset($app['assetic.path_to_source']) ? $app['assetic.path_to_source'] : $app['assetic.path_to_web'];
            $factory = new AssetFactory($root, $app['assetic.options']['debug']);
            $factory->setAssetManager($app['assetic.asset_manager']);
            $factory->setFilterManager($app['assetic.filter_manager']);
            if ($app['config']->get('asset::config.cachebusting') and ! $app['assetic.options']['debug']) {
                $factory->addWorker(new CacheBustingWorker());
            }
            return $factory;
        });
    }

    /**
     * Registers asset writer, writes to the 'assetic.path_to_web' folder
     */
    protected function registerAsseticWriter()
    {
        $this->app['assetic.asset_writer'] = $this->app->share(function ($app) {
            return new CheckedAssetWriter($app['assetic.path_to_web']);
        });
    }

    /**
     * Registers assetic file manager
     */
    protected function registerAsseticFilterManager()
    {
        $this->app['assetic.filter_manager'] = $this->app->share(function ($app) {
            $fm = new FilterManager();
            if ($app['config']->has('asset::config.filter_manager')) {
                $callback = $app['config']->get('asset::config.filter_manager');
                if (is_callable($callback)) {
                    $callback($fm);
                }
            }
            return $fm;
        });
    }

    /**
     * Registers base assetic manager
     */
    protected function registerAsseticManager()
    {
        $this->app['assetic.asset_manager'] = $this->app->share(function ($app) {
            $am = new AssetManager();
            if ($app['config']->has('asset::config.asset_manager')) {
                $callback = $app['config']->get('asset::config.asset_manager');
                if (is_callable($callback)) {
                    $callback($am);
                }
            }
            return $am;
        });
    }

    /**
     * Register lazy asset manager for loading assets from $app['assetic.formulae']
     */
    protected function registerAsseticLazyManager()
    {
        $this->app['assetic.lazy_asset_manager'] = $this->app->share(function ($app) {
            $formulae = isset($app['assetic.formulae']) ? $app['assetic.formulae'] : array();
            $options  = $app['assetic.options'];
            $lazy     = new LazyAssetmanager($app['assetic.factory']);
            if (empty($formulae)) {
                return $lazy;
            }
            foreach ($formulae as $name => $formula) {
                $lazy->setFormula($name, $formula);
            }
            if ($options['formulae_cache_dir'] !== null && $options['debug'] !== true) {
                foreach ($lazy->getNames() as $name) {
                    $lazy->set($name, new AssetCache(
                            $lazy->get($name), new FilesystemCache($options['formulae_cache_dir'])
                    ));
                }
            }
            return $lazy;
        });
    }

    /**
     * Registers assetic dumper
     */
    protected function registerAsseticDumper()
    {
        $this->app['assetic.dumper'] = $this->app->share(function ($app) {
            return new Dumper(
                    $app['assetic.asset_manager'], $app['assetic.lazy_asset_manager'], $app['assetic.asset_writer'], $app['view']->getFinder()
            );
        });
    }

    /**
     * Registers assetic command
     */
    protected function registerAsseticCommand()
    {
        $this->app['command.assetic.build'] = $this->app->share(function($app) {
            return new Console\AsseticBuildCommand();
        });
        $this->commands('command.assetic.build');
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
            'assetic', 'assetic.factory', 'assetic.dumper', 'assetic.filters',
            'assetic.asset_manager', 'assetic.filtermanager', 'assetic.lazy_asset_manager',
            'assetic.asset_writer', 'command.assetic.build',
            'antares.asset', 'antares.asset.dispatcher',
            'antares.asset.resolver', 'asset.symlinker', 'antares.asset.publisher'
        ];
    }

}

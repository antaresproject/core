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


namespace Antares\View;

use Antares\View\Theme\Finder;
use Antares\View\Theme\ThemeManager;
use Illuminate\View\ViewServiceProvider as ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../resources/config/helpers.php', 'view-helpers');

        $this->registerEngineResolver();

        $this->registerViewFinder();

        $this->registerFactory();

        $this->registerTheme();
    }

    /**
     * Register the service provider for view finder.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $this->app->singleton('view.finder', function ($app) {
            $paths = $app->make('config')->get('view.paths');
            return new FileViewFinder($app->make('files'), $paths);
        });
    }

    /**
     * Register the service provider for theme.
     *
     * @return void
     */
    protected function registerTheme()
    {
        $this->app->singleton('antares.theme', function ($app) {
            return new ThemeManager($app);
        });

        $this->app->singleton('antares.theme.finder', function ($app) {
            return new Finder($app);
        });
    }

    /**
     * booting service provider
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/../');
        $this->loadViewsFrom("{$path}/resources/views/helpers", 'view-helpers');
    }

}

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

namespace Antares\Memory;

use Antares\Support\Providers\ServiceProvider;
use Antares\Memory\Repository\Resource;
use Antares\Model\Component;

class MemoryServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('antares.resource.repository', function ($app) {
            $driver = $app->make('cache')->driver();
            return $app->make(Resource::class, [$app, $driver]);
        });

        $this->app->singleton('antares.memory', function ($app) {
            $manager   = new MemoryManager($app);
            $namespace = $this->hasPackageRepository() ? 'antares/memory::' : 'antares.memory';
            $manager->setConfig($app->make('config')->get($namespace));
            return $manager;
        });
        $this->app->bind('antares.component', function () {
            return new Component();
        });
        $this->app->bind('antares.defered.service', function () {
            return new DeferedService();
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/../resources');
        $this->addConfigComponent('antares/memory', 'antares/memory', $path . '/config');
        if (!$this->hasPackageRepository()) {
            $this->bootUnderLaravel($path);
        }
        $this->bootMemoryEvent();
        Model\Permission::observe(new Observer\PermissionObserver());
        \Antares\Model\UserRole::observe(new Observer\PermissionObserver());
        \Antares\Model\Role::observe(new Observer\PermissionObserver());
        \Antares\Model\Action::observe(new Observer\PermissionObserver());
    }

    /**
     * Register memory events during booting.
     *
     * @return void
     */
    protected function bootMemoryEvent()
    {
        $app = $this->app;
        /** looking for forms * */
        $app->make('events')->listen('antares.forms', function ($name) use ($app) {
            $app->make('antares.memory')->make('registry.forms')->put('forms', $name);
        });
        /** when application is terminating we collect all data from application * */
        $app->terminating(function () use ($app) {
            if (!app('antares.installed')) {
                //$app->make('antares.memory')->finish();
            }
        });
        $app->make('events')->listen('antares.after.load-service-providers', function() {
            $this->app->make('antares.defered.service')->run();
        });
    }

    /**
     * Boot under Laravel setup.
     *
     * @param  string  $path
     *
     * @return void
     */
    protected function bootUnderLaravel($path)
    {
        $this->mergeConfigFrom("{$path}/config/config.php", 'antares.memory');

        $this->publishes([
            "{$path}/config/config.php"   => config_path('antares/memory.php'),
            "{$path}/database/migrations" => database_path('migrations'),
        ]);
    }

}

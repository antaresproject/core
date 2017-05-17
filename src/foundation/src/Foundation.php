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

namespace Antares\Foundation;

use Antares\Contracts\Foundation\Foundation as FoundationContract;
use Antares\Logger\Http\Middleware\LoggerMiddleware;
use Antares\Contracts\Memory\Provider;
use Antares\Extension\RouteGenerator;
use Antares\Http\RouteManager;
use Exception;
use Closure;

class Foundation extends RouteManager implements FoundationContract
{

    /**
     * Booted indicator.
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * Get acl services.
     *
     * @var \Antares\Contracts\Authorization\Authorization
     */
    public function acl()
    {
        return $this->app->make('antares.platform.acl');
    }

    /**
     * Start the application.
     *
     * @return $this
     */
    public function boot()
    {
        if (!$this->booted) {
            $this->booted = true;

            $this->bootApplication();
        }


        return $this;
    }

    /**
     * Get installation status.
     *
     * @return bool
     */
    public function installed()
    {
        return (bool) $this->app->make('antares.installed');
    }

    /**
     * Get memory services.
     *
     * @var \Antares\Contracts\Memory\Provider
     */
    public function memory()
    {
        return $this->app->make('antares.platform.memory');
    }

    /**
     * Get menu services.
     *
     * @var \Antares\UI\Menu
     */
    public function menu()
    {
        return $this->app->make('antares.platform.menu');
    }

    /**
     * Register the given Closure with the "group" function namespace set.
     *
     * @param  string|null  $namespace
     * @param  \Closure|null  $callback
     *
     * @return void
     */
    public function namespaced($namespace, Closure $callback)
    {
        $attributes = [];

        if (!empty($namespace) && $namespace != '\\') {
            $attributes['namespace'] = $namespace;
        }
        $attributes['middleware'] = ['antares', LoggerMiddleware::class];

        $this->group('antares/foundation', 'antares', $attributes, $callback);
    }

    /**
     * Get extension route.
     *
     * @param  string  $name
     * @param  string  $default
     *
     * @return \Antares\Contracts\Extension\RouteGenerator
     */
    public function route($name, $default = '/')
    {
        $this->boot();

        if (in_array($name, ['antares', 'antares/foundation'])) {
            $name = 'antares';
        }
        return parent::route($name, $default);
    }

    /**
     * Boot application.
     *
     * @return void
     */
    protected function bootApplication()
    {

        $this->registerBaseServices();
        try {
            $memory = $this->bootInstalledApplication();
        } catch (Exception $e) {
            $memory = $this->bootNewApplication();
        }
        $this->app->instance('antares.platform.memory', $memory);
        $this->registerComponents($memory);
        $this->app->make('events')->fire('antares.started', [$memory]);
    }

    /**
     * Run booting on installed application.
     *
     * @return \Antares\Contracts\Memory\Provider
     *
     * @throws \Exception
     */
    protected function bootInstalledApplication()
    {
        $memory = $this->app->make('antares.memory')->make();
        if (is_null($memory->get('app.installed'))) {
            throw new Exception('Installation is not completed');
        }

        $this->acl()->attach($memory);

        $this->app['antares.installed'] = true;
        $this->createAdminMenu();
        return $memory;
    }

    /**
     * Run booting on new application.
     *
     * @return \Antares\Contracts\Memory\Provider
     */
    protected function bootNewApplication()
    {
        $memory = $this->app->make('antares.memory')->make('runtime.antares');
        $memory->put('site.name', 'Antares');

        $this->menu()->add('install')
                ->link($this->handles('antares::install'));

        $this->app['antares.installed'] = false;
        return $memory;
    }

    /**
     * Create Administration Menu for Antares.
     *
     * @return void
     */
    protected function createAdminMenu()
    {
        $config = config('menu');
        $events = $this->app->make('events');
        foreach ($config as $event => $ordered) {
            foreach ($ordered as $name => $value) {
                if (is_numeric($name)) {
                    $events->listen($event, $value);
                } else {
                    $events->listen($event, $name, $value);
                }
            }
        }
    }

    /**
     * Register base application services.
     *
     * @return void
     */
    protected function registerBaseServices()
    {
        $widget = $this->app->make('antares.widget');
        $this->app->instance('antares.platform.menu', $widget->make('menu.antares'));
        $this->app->instance('antares.platform.acl', $this->app->make('antares.acl')->make('antares'));
        $this->app->instance('app.menu', $widget->make('menu.app'));
    }

    /**
     * Register base application components.
     *
     * @param  \Antares\Contracts\Memory\Provider  $memory
     *
     * @return void
     */
    protected function registerComponents(Provider $memory)
    {
        $this->app->make('antares.notifier')->setDefaultDriver('antares');
        $this->app->make('antares.notifier.email')->attach($memory);
    }

    /**
     * {@inheritdoc}
     */
    protected function generateRouteByName($name, $default)
    {
        $segment = request()->segment(1);
        if (in_array($name, ['antares']) && $segment !== 'install') {
            $level = app('antares.areas')->findMatched($segment, $this->app->make('config')->get('antares/foundation::handles', $default));
            return new RouteGenerator($level, $this->app->make('request'));
        }
        return parent::generateRouteByName($name, $default);
    }

    /**
     * Magic method to get services.
     *
     * @param  string   $method
     * @param  array    $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->app, $method], $parameters);
    }

    /**
     * @return type
     */
    public function app()
    {
        return $this->app;
    }

}

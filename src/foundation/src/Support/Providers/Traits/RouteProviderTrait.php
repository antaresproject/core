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

namespace Antares\Foundation\Support\Providers\Traits;

use Antares\Area\Facade\AreasManager;
use Illuminate\Routing\Router;

trait RouteProviderTrait
{

    /**
     * Load the backend routes file for the application.
     *
     * @param  string  $path
     * @param  string|null  $namespace
     *
     * @return void
     */
    protected function loadBackendRoutesFrom($path, $namespace = null)
    {
        if (AreasManager::manager()->isFrontendArea()) {
            return;
        }

        if (!$this->isPathIncluded($path)) {
            $foundation = $this->app->make('antares.app');
            $namespace  = $namespace ?: $this->namespace;
            $foundation->namespaced($namespace, $this->getRouteLoader($path));
        }
    }

    /**
     * Load the frontend routes file for the application.
     *
     * @param  string  $path
     * @param  string|null  $namespace
     *
     * @return void
     */
    protected function loadFrontendRoutesFrom($path, $namespace = '', array $attributes = [])
    {

        if (AreasManager::manager()->isBackendArea()) {
            return;
        }


        if (!$this->isPathIncluded($path)) {
            $foundation = $this->app->make('antares.app');
            $namespace  = $namespace ?: $this->namespace;

            $attributes = [];

            if (!empty($namespace) && $namespace != '\\') {
                $attributes['namespace'] = $namespace;
            }

            $attributes['middleware'] = ['antares', 'Antares\Foundation\Http\Middleware\UseFrontendTheme'];
            $foundation->group('antares/foundation', 'antares', $attributes, $this->getRouteLoader($path));
        }
    }

    /**
     * Resolve route group attributes.
     *
     * @param  array|string|null  $namespace
     * @param  array  $attributes
     *
     * @return array
     */
    protected function resolveRouteGroupAttributes($namespace = null, array $attributes = [])
    {
        if (is_array($namespace)) {
            $attributes = $namespace;
            $namespace  = '';
        }
        if (!is_null($namespace)) {
            $attributes['namespace'] = empty($namespace) ? $this->namespace : "{$this->namespace}\\{$namespace}";
        }
        return $attributes;
    }

    /**
     * Build route generator callback.
     *
     * @param  string  $path
     *
     * @return \Closure
     */
    protected function getRouteLoader($path)
    {
        return function (Router $router) use ($path) {
            require $path;
        };
    }

    /**
     * Check if the given route path has been already included.
     *
     * @param $path
     * @return bool
     */
    protected function isPathIncluded($path)
    {
        return in_array($path, get_included_files(), true);
    }

    /**
     * Create an event listener for `antares.extension: booted` to allow
     * application to be loaded only after extension routing.
     *
     * @param  \Closure|string  $callback
     */
    protected function afterExtensionLoaded($callback)
    {
        $this->app->make('antares.extension')->after($callback);
    }

}

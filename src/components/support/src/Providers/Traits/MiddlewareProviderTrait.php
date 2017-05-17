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

namespace Antares\Support\Providers\Traits;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use Antares\Foundation\Http\Middleware\ModuleMiddleware;
use Antares\Form\Middleware\FormMiddleware;

trait MiddlewareProviderTrait
{

    /**
     * suffix middleware key name
     *
     * @var type 
     */
    private static $withSuffix = 'can';

    /**
     * Register route middleware.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @param  \Illuminate\Contracts\Http\Kernel  $kernel
     *
     * @return void
     */
    protected function registerRouteMiddleware(Router $router, Kernel $kernel)
    {


        foreach ((array) $this->middleware as $middleware) {
            $kernel->pushMiddleware($middleware);
        }
        foreach ((array) $this->middlewareGroups as $key => $middleware) {
            $router->middlewareGroup($key, $middleware);
        }

        $moduleName                         = $this->resolveModuleName();
        $this->routeMiddleware[$moduleName] = FormMiddleware::class;
        if (empty($this->routeMiddleware) && isset($this->routeGroup) && $this->routeGroup !== 'app') {
            $router->aliasMiddleware($moduleName, ModuleMiddleware::class);
        } else {
            foreach ((array) $this->routeMiddleware as $key => $middleware) {
                $router->aliasMiddleware($key, $middleware);
            }
        }
    }

    /**
     * middleware route keyname creator
     * 
     * @return String
     */
    protected function resolveModuleName()
    {
        if (!isset($this->routeGroup)) {
            return '';
        }
        return join('.', [str_replace('/', '.', $this->routeGroup), self::$withSuffix]);
    }

}

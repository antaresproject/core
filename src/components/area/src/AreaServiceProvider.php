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

namespace Antares\Area;

use Antares\Support\Providers\ServiceProvider;
use Antares\Area\Contracts\AreaManagerContract;
use Antares\Area\Middleware\AreaMiddleware;
use Illuminate\Routing\Router;
use Antares\Area\AreaManager;

class AreaServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AreaManagerContract::class, AreaManager::class);
    }

    /**
     * Boot the service provider and bind the {area} wildcard to the router.
     */
    public function boot()
    {

        $router = $this->app->make(Router::class);
        $router->bind('area', function($value) {
            return $this->app->make(AreaManagerContract::class)->getById($value);
        });

        $router->pushMiddlewareToGroup('web', AreaMiddleware::class);
    }

}

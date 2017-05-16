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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Arcanedev\Support\Providers\RouteServiceProvider as ServiceProviderG;
use Illuminate\Contracts\Routing\Registrar as Router;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * Get Route attributes
     *
     * @return array
     */
    public function routeAttributes()
    {
        return array_merge(['prefix' => 'admin', 'middleware' => null,], [
            'namespace' => 'Antares\\Logger\\Http\\Admin\\Controllers',
        ]);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     */
    public function map(Router $router)
    {
        $router->group($this->routeAttributes(), function(Router $router) {
            \Antares\Logger\Http\Routes\LoggerRoute::register($router);
        });
    }

}

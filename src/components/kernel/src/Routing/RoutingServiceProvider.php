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

namespace Antares\Routing;

use Illuminate\Routing\RoutingServiceProvider as ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class RoutingServiceProvider extends ServiceProvider
{

    /**
     * Register the router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {

        $this->app->singleton('router', function (Application $app) {
            return new Router($app->make('events'), $app);
        });
    }

}

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


namespace Antares\Support\Providers;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use Antares\Support\Providers\Traits\FilterProviderTrait;
use Antares\Support\Providers\Traits\MiddlewareProviderTrait;

abstract class PipelineServiceProvider extends ServiceProvider
{

    use FilterProviderTrait,
        MiddlewareProviderTrait;

    /**
     * Bootstrap the application events.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @param  \Illuminate\Contracts\Http\Kernel  $kernel
     *
     * @return void
     */
    public function boot(Router $router, Kernel $kernel)
    {
        $this->registerRouteFilters($router);

        $this->registerRouteMiddleware($router, $kernel);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
    }

}

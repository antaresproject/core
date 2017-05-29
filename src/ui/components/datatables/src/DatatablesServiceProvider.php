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

namespace Antares\Datatables;

use Yajra\Datatables\DatatablesServiceProvider as SupportDatatablesServiceProvider;
use Antares\Datatables\Adapter\GroupsFilterAdapter;
use Illuminate\Routing\Router;

class DatatablesServiceProvider extends SupportDatatablesServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Datatables::class);
        $this->app->singleton(GroupsFilterAdapter::class);
        $this->app->alias(Datatables::class, 'datatables');
    }

    /**
     * Booting service provider
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/../');
        $this->loadViewsFrom("{$path}/resources/views", 'datatables-helpers');
        $this->mergeConfigFrom("{$path}/resources/config/config.php", 'datatables-config');


        $this->app->make('antares.app')->namespaced(null, function (Router $router) {
            require __DIR__ . DIRECTORY_SEPARATOR . 'backend.php';
        });
    }

}

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

namespace Antares\Foundation\Providers;

use Antares\Foundation\Listeners\DatatableMassActionsDependableActions;
use Antares\Foundation\Listeners\BreadcrumbsMenuDependableActions;
use Antares\Foundation\Listeners\DatatableDependableActions;
use Antares\Support\Providers\ServiceProvider;

class DependableActionsServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $events = $this->app->make('events');
        $events->listen('datatables:*:before.action.edit', DatatableDependableActions::class);
        $events->listen('datatables:*:after.massactions.action.delete', DatatableMassActionsDependableActions::class);
        $events->listen('breadcrumb.before.render.*', BreadcrumbsMenuDependableActions::class);
    }

}

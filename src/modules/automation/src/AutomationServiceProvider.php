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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation;

use Antares\Automation\Http\Handlers\AutomationLogsBreadcrumbMenu;
use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Antares\Automation\Console\QueueCommand;
use Antares\Automation\Console\SyncCommand;
use Antares\Automation\Jobs\SyncAutomation;

class AutomationServiceProvider extends ModuleServiceProvider
{

    use DispatchesJobs;

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Automation\Http\Controllers\Admin';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/automation';

    /**
     * bindable dependency injection params
     *
     * @var array
     */
    protected $di = [
        'Antares\Automation\Contracts\IndexPresenter' => 'Antares\Automation\Http\Presenters\IndexPresenter'
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function bootExtensionComponents()
    {

        $this->listenEvents();
        $this->attachMenu(AutomationLogsBreadcrumbMenu::class);
    }

    /**
     * Registers service provider
     */
    public function register()
    {
        parent::register();
        $this->commands([SyncCommand::class, QueueCommand::class]);
    }

    /**
     * component event listeners
     */
    protected function listenEvents()
    {

        listen('after.activated.antaresproject/component-automation', function() {
            $watchDog = $this->app->make('antares.watchdog');
            $watchDog->up('automation:start');
            $watchDog->up('queue:start');
            $job      = $this->app->make(SyncAutomation::class)->onQueue('install');
            return $this->dispatch($job);
        });
    }

}

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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications;

use Antares\Notifications\Http\Handlers\NotificationsBreadcrumbMenu;
use Antares\Foundation\Http\Handlers\NotificationsTopMenuHandler;
use Antares\Notifications\Console\NotificationCategoriesCommand;
use Antares\Notifications\Console\NotificationSeveritiesCommand;
use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Notifications\Console\NotificationTypesCommand;
use Antares\Notifications\Listener\NotificationsListener;
use Antares\Notifications\Listener\ConfigurationListener;
use Antares\Notifications\Console\NotificationsRemover;
use Antares\Control\Http\Handlers\ControlPane;
use Antares\Memory\Model\Option;

class NotificationsServiceProvider extends ModuleServiceProvider
{

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Notifications\Http\Controllers\Admin';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/notifications';

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        "antares.form: foundation.settings" => ConfigurationListener::class,
    ];

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->bindContracts();
        $this->app->singleton('notifications.contents', function ($app) {
            return new Contents();
        });
        $this->commands([
            NotificationCategoriesCommand::class,
            NotificationSeveritiesCommand::class,
            NotificationTypesCommand::class
        ]);
    }

    /**
     * Boot extension routing.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $path = __DIR__;
        $this->loadBackendRoutesFrom("{$path}/Http/backend.php");
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function bootExtensionComponents()
    {
        $path = __DIR__ . '/../';
        $this->addConfigComponent('antares/notifications', 'antares/notifications', "{$path}/resources/config");
        $this->addLanguageComponent('antares/notifications', 'antares/notifications', "{$path}/resources/lang");
        $this->addViewComponent('antares/notifications', 'antares/notifications', "{$path}/resources/views");
        $this->bootMemory();
        $this->listenEvents();
        $this->attachMenu(NotificationsTopMenuHandler::class);
        if (config('antares/notifications::sockets')) {
            publish('notifications', 'scripts.default');
        }
        $this->attachMenu(NotificationsBreadcrumbMenu::class);
        $this->app->make('view')->composer('antares/notifications::admin.logs.config', ControlPane::class);
        $this->commands([
            NotificationsRemover::class
        ]);
        Option::observe(new ConfigurationListener());
    }

    /**
     * booting events
     */
    protected function bootMemory()
    {
        $this->app->make('antares.acl')->make($this->routeGroup)->attach(
                $this->app->make('antares.platform.memory')
        );
    }

    /**
     * Component event listeners
     */
    protected function listenEvents()
    {
        $this->app->make(NotificationsListener::class)->listen();
    }

}

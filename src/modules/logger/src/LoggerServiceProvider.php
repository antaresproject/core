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



namespace Antares\Logger;

use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Logger\Http\Handlers\ActivityLogsBreadcrumbMenu;
use Antares\Logger\Http\Handlers\RequestLogBreadcrumbMenu;
use Antares\Logger\Console\LogsTranslationSynchronizer;
use Illuminate\Contracts\Routing\Registrar as Router;
use Antares\Logger\Http\Middleware\LoggerMiddleware;
use Antares\Logger\Http\Handlers\ErrorLogBreadcrumb;
use Antares\Logger\Listeners\UserAuthListener;
use Antares\Logger\Observer\LoggerObserver;
use Antares\Logger\Console\ReportCommand;
use Antares\Logger\Utilities\Filesystem;
use Antares\Logger\Event\CustomLog;
use Antares\Logger\Memory\Handler;
use Antares\Memory\Provider;

class LoggerServiceProvider extends ModuleServiceProvider
{

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Logger\Http\Controllers\Admin';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/logger';

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'logger.custom' => CustomLog::class,
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        UserAuthListener::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'antares.logger.middleware' => LoggerMiddleware::class,
    ];

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->bindContracts();
        $this->app->singleton('antares.logger', function ($app) {
            return new Factory($app);
        });
        $this->app->singleton('logger.log-viewer.menu', function ($app) {
            return $app->make('Antares\Logger\Utilities\LogMenu');
        });
        $this->app->singleton('logger.filesystem', function ($app) {
            $files = $app->make('files');
            return new Filesystem($files, storage_path('logs'));
        });

        $this->commands(ReportCommand::class);
        $this->commands(LogsTranslationSynchronizer::class);

        $this->app['antares.logger.installed'] = true;
    }

    /**
     * Boot extension routing.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $path = __DIR__;
        $this->loadBackendRoutesFrom("{$path}/routes.php");
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function bootExtensionComponents()
    {
        $path = __DIR__ . '/../';
        $this->addConfigComponent('antares/logger', 'antares/logger', "{$path}/resources/config");
        $this->addLanguageComponent('antares/logger', 'antares/logger', "{$path}/resources/lang");
        $this->addViewComponent('antares/logger', 'antares/logger', "{$path}/resources/views");
        $this->bootMemory();
        $this->app->register('Antares\\Logger\\Providers\\RouteServiceProvider');
        $this->attachMenu(ErrorLogBreadcrumb::class);
        $this->attachMenu(ActivityLogsBreadcrumbMenu::class);
        $this->attachMenu(RequestLogBreadcrumbMenu::class);
        $this->observeLogger();
    }

    /**
     * booting events
     */
    protected function bootMemory()
    {
        $this->app->make('antares.acl')->make($this->routeGroup)->attach(
                $this->app->make('antares.platform.memory')
        );
        $app    = $this->app;
        $config = $app->make('config');
        $app->make('antares.memory')->extend('checksum', function ($app, $name) use($config) {
            $handler = new Handler('checksum', $config->get('antares/logger::memory'), $app);
            return new Provider($handler);
        });
    }

    /* ------------------------------------------------------------------------------------------------
      |  Main Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     */
    public function map(Router $router)
    {
        $router->group($this->routeAttributes(), function(Router $router) {
            LogViewerRoute::register($router);
        });
    }

    /**
     * Observers logger while creating new log entry
     */
    protected function observeLogger()
    {
        $this->app->make('antares.logger')->getMainModel()->observe(new LoggerObserver, 1);
    }

}

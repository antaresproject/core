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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater;

use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Logger\Http\Handlers\SandboxBreadcrumbMenu;
use Antares\Logger\Http\Handlers\BackupBreadcrumbMenu;
use Antares\Updater\Composers\SandboxPlaceholder;
use Antares\Updater\Console\FilesBackupCommand;
use Antares\Updater\Console\RestoreAppCommand;
use Antares\Updater\Console\AppBackupCommand;
use Antares\Updater\Console\DbBackupCommand;
use Antares\Updater\Events\VersionChecker;
use Antares\Updater\Events\SandboxMode;
use Antares\Updater\Factory;
use Antares\Updater\Version;

class UpdaterServiceProvider extends ModuleServiceProvider
{

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Updater\Http\Controllers\Admin';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/updater';

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->bindContracts();

        $this->app->singleton('antares.version', function ($app) {
            return new Factory($app);
        });
        $this->app->singleton('antares.version', function ($app) {
            return new Version($app);
        });
        $this->commands([RestoreAppCommand::class, AppBackupCommand::class, DbBackupCommand::class, FilesBackupCommand::class]);
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

        $view = $this->app->make('view');
        $view->composer('antares/foundation::*', SandboxPlaceholder::class);

        $this->addConfigComponent('antares/updater', 'antares/updater', "{$path}/resources/config");
        $this->addLanguageComponent('antares/updater', 'antares/updater', "{$path}/resources/lang");
        $this->addViewComponent('antares/updater', 'antares/updater', "{$path}/resources/views");
        $this->bootMemory();
        $this->attachMenu(SandboxBreadcrumbMenu::class);
        $this->attachMenu(BackupBreadcrumbMenu::class);

        $events = $this->app->make('events');
        $events->listen('version.check', VersionChecker::class);
        $events->listen('sandbox.mode', SandboxMode::class);
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

}

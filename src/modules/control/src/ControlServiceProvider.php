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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Control;

use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Control\Http\Handlers\GroupsBreadcrumbMenu;
use Antares\Control\Http\Handlers\UsersBreadcrumbMenu;
use Antares\Control\Http\Handlers\ModulesPane;
use Antares\Control\Http\Handlers\ControlPane;
use Antares\Control\Http\Handlers\StaffPane;

class ControlServiceProvider extends ModuleServiceProvider
{

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Control\Http\Controllers';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/control';

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->bindContracts();
    }

    /**
     * Boot extension components.
     *
     * @return void
     */
    protected function bootExtensionComponents()
    {
        $path = __DIR__ . '/../resources';
        $this->addConfigComponent('antares/control', 'antares/control', "{$path}/config");
        $this->addLanguageComponent('antares/control', 'antares/control', "{$path}/lang");
        $this->addViewComponent('antares/control', 'antares/control', "{$path}/views");
        $this->bootMenu();
        $this->bootMemory();
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
     * booting menu
     */
    protected function bootMenu()
    {
        $view = $this->app->make('view');
        $view->composer('antares/control::acl.*', function () {
            return ModulesPane::getInstance()->make();
        });
        $this->attachMenu([GroupsBreadcrumbMenu::class, UsersBreadcrumbMenu::class]);
        $view->composer('antares/foundation::settings.*', ControlPane::class);
        $view->composer('antares/control::*', StaffPane::class);
    }

    /**
     * booting acl memory
     */
    protected function bootMemory()
    {
        $this->app->make('antares.acl')->make($this->routeGroup)->attach(
                $this->app->make('antares.platform.memory')
        );
    }

}

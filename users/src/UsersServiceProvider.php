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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Users;

use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Users\Http\Handler\UsersActivityPlaceholder;
use Antares\Users\Http\Handlers\UserViewBreadcrumbMenu;
use Antares\Users\Http\Handlers\UserEditBreadcrumbMenu;
use Antares\Users\Http\Handlers\UsersBreadcrumbMenu;
use Antares\Contracts\Auth\Command\ThrottlesLogins;
use Antares\Users\Auth\BasicThrottle;
use Antares\Foundation\MenuComposer;
use Antares\Users\Http\Middleware\CaptureUserActivityMiddleware;
use Antares\Users\Memory\Avatar;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;

class UsersServiceProvider extends ModuleServiceProvider
{

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Users\Http\Controllers';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/users';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerThrottlesLogins();
        $this->app->singleton(Avatar::class, function ($app) {
            return new Avatar();
        });
    }

    /**
     * Register the service provider for foundation.
     *
     * @return void
     */
    protected function registerThrottlesLogins()
    {
        $config    = $this->app->make('config')->get('antares/foundation::throttle', []);
        $throttles = isset($config['resolver']) ? $config['resolver'] : BasicThrottle::class;
        $this->app->bind(ThrottlesLogins::class, $throttles);
        BasicThrottle::setConfig($config);
    }

    /**
     * @param Router $router
     * @return void
     */
    protected function registerUsersActivity(Router $router)
    {
        Event::listen('antares.ready: admin', UsersActivityPlaceholder::class);
        $router->pushMiddlewareToGroup('web', CaptureUserActivityMiddleware::class);
    }

    /**
     * users service boot
     * 
     * @param Router $router
     */
    public function boot(Router $router)
    {
        $path = __DIR__ . '/../';
        $this->addConfigComponent($this->routeGroup, $this->routeGroup, "{$path}/resources/config");
        $this->addLanguageComponent($this->routeGroup, $this->routeGroup, "{$path}/resources/lang");
        if (!$this->app->routesAreCached()) {
            require "frontend.php";
        }
        $path = __DIR__;
        $this->loadBackendRoutesFrom("{$path}/backend.php");
        MenuComposer::getInstance()->compose(UsersBreadcrumbMenu::class);
        $this->attachMenu([UserViewBreadcrumbMenu::class, UserEditBreadcrumbMenu::class]);
        $this->registerUsersActivity($router);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Avatar::class];
    }

}

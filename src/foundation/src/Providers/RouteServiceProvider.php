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

use Illuminate\Routing\Router;
use Illuminate\Contracts\Http\Kernel;
use Antares\Users\Http\Middleware\Can;
use Antares\Users\Http\Middleware\CanManage;
use Antares\Users\Http\Middleware\Authenticate;
use Antares\Users\Http\Middleware\CanRegisterUser;
use Antares\Foundation\Http\Middleware\CanBeInstalled;
use Antares\Foundation\Http\Middleware\VerifyCsrfToken;
use Antares\Users\Http\Middleware\RedirectIfAuthenticated;
use Antares\Foundation\Http\Middleware\RedirectIfInstalled;
use Antares\Support\Providers\Traits\MiddlewareProviderTrait;
use Antares\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    use MiddlewareProviderTrait;

    /**
     * The application's middleware stack.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'antares.auth'        => Authenticate::class,
        'antares.can'         => Can::class,
        'antares.csrf'        => VerifyCsrfToken::class,
        'antares.guest'       => RedirectIfAuthenticated::class,
        'antares.installable' => CanBeInstalled::class,
        'antares.installed'   => RedirectIfInstalled::class,
        'antares.manage'      => CanManage::class,
        'antares.registrable' => CanRegisterUser::class
    ];

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $router = $this->app->make(Router::class);
        $kernel = $this->app->make(Kernel::class);
        $this->registerRouteMiddleware($router, $kernel);
        $this->app->make('events')->fire('antares.ready');
    }

    /**
     * Load the application routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $path = realpath(__DIR__ . '/../../');
        require "{$path}/src/routes.php";
    }

}

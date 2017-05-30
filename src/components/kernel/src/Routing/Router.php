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
 * @version    0.9.2
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Routing;

use Illuminate\Routing\ResourceRegistrar as BaseResourceRegistrar;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Routing\Router as BaseRouter;
use Illuminate\Support\Arr;

class Router extends BaseRouter
{

    /**
     * Register the typical authentication routes for an application.
     *
     * @return void
     */
    public function auth()
    {
        // Authentication Routes...        
        $this->get('login', 'Auth\AuthenticateController@show');
        $this->post('login', 'Auth\AuthenticateController@attempt');
        $this->get('logout', 'Auth\DeauthenticateController@logout');
    }

    /**
     * Register the typical password reset routes for an application.
     *
     * @return void
     */
    public function password()
    {
        // Password Reset Routes...
        $this->get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
        $this->post('password/reset', 'Auth\PasswordController@reset');
        $this->get('password/email', 'Auth\PasswordController@showLinkRequestForm');
        $this->post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array   $options
     *
     * @return void
     */
    public function resource($name, $controller, array $options = [])
    {
        if ($this->container && $this->container->bound(BaseResourceRegistrar::class)) {
            $registrar = $this->container->make(BaseResourceRegistrar::class);
        } else {
            $registrar = new ResourceRegistrar($this);
        }

        $registrar->register($name, $controller, $options);
    }

    /**
     * Gather the middleware for the given route.
     *
     * @param  LaravelRoute  $route
     *
     * @return array
     */
    public function gatherRouteMiddlewares(LaravelRoute $route)
    {
        $middlewares = [];

        foreach ($route->middleware() as $name) {
            $middlewares[] = $this->resolveMiddlewareClassName($name);
        }
        return Arr::flatten($middlewares);
    }

    /**
     * Create a new Route object.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  mixed  $action
     * @return \Illuminate\Routing\Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))->setRouter($this)->setContainer($this->container);
    }

}

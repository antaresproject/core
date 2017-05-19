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

namespace Antares\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\NamespacedItemResolver;
use Illuminate\Support\Arr;
use Exception;
use Closure;

abstract class RouteManager
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * List of routes.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Construct a new instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     *  Return locate handles configuration for a package/app.
     *
     * @param  string  $path
     * @param  array   $options
     *
     * @return array
     */
    public function locate($path, array $options = [])
    {
        $query = '';

        if (strpos($path, '?') !== false) {
            list($path, $query) = explode('?', $path, 2);
        }
        list($package, $route, $item) = with(new NamespacedItemResolver())->parseKey($path);


        $route   = $this->prepareValidRoute($route, $item, $query, $options);
        empty($package) && $package = 'app';
        return [$package, $route];
    }

    /**
     * Return route group dispatch for a package/app.
     *
     * @param  string  $name
     * @param  string  $default
     * @param  array|\Closure  $attributes
     * @param  \Closure|null  $callback
     *
     * @return array
     */
    public function group($name, $default, $attributes = [], Closure $callback = null)
    {

        $route      = $this->route($name, $default);
        $attributes = array_merge($attributes, [
            'prefix' => $route->prefix(),
            'domain' => $route->domain(),
        ]);

        if (!is_null($callback)) {
            $this->app->make('router')->group($attributes, $callback);
        }
        return $attributes;
    }

    /**
     *  Return handles URL for a package/app.
     *
     * @param  string  $path
     * @param  array   $options
     *
     * @return string
     */
    public function handles($path, array $options = [])
    {

        if (str_contains($path, '.') and ! is_api_request() and ! starts_with($path, 'http') && strpos($path, '.') < 20) {
            try {
                return route($path, $options);
            } catch (Exception $ex) {
                return '';
            }
        }
        $url = $this->app->make('url');
        if ($url->isValidUrl($path)) {
            return $path;
        }
        list($package, $route) = $this->locate($path, $options);

        $element = $this->route($package);
        $element->setAreaPrefix($element);
        $locate  = $element->to($route);
        empty($locate) && $locate  = '/';
        $sandbox = (isset($options['sandbox']) && $options['sandbox'] == false) ? null : $url->getRequest()->query('sandbox');
        if (!is_null($sandbox)) {
            $separator = (parse_url($locate, PHP_URL_QUERY) == NULL) ? '?' : '&';
            $locate    .= $separator . 'sandbox=' . $sandbox;
        }
        return $url->to($locate);
    }

    /**
     *  Return handles URL for a package/app.
     *
     * @param  string  $path
     * @param  array   $options
     *
     * @return string
     */
    public function handles_acl($path, array $options = [])
    {
        $url = $this->app->make('url');
        if ($url->isValidUrl($path)) {
            return $path;
        }
        @list($component, $action) = explode('/', $path);
        if (is_null($component) or is_null($action)) {
            return $this->handles($path, $options);
        }
//        $resourceMap = app('antares.resource.repository')->findOneByAttributes(['component' => $component, 'action' => $action]);
//        if (is_null($resourceMap)) {
//            return $this->handles($path, $options);
//        }
//        $resource  = $resourceMap->resource;
//        $component = str_replace('::', '/', $component);
//        if (!app('antares.acl')->make($component)->can($resource)) {
//            return '';
//        }

        return $this->handles($path, $options);
    }

    /**
     *  Return if handles URL match given string.
     *
     * @param  string  $path
     *
     * @return bool
     */
    public function is($path)
    {
        list($package, $route) = $this->locate($path);

        return $this->route($package)->is($route);
    }

    /**
     * Get extension route.
     *
     * @param  string  $name
     * @param  string  $default
     *
     * @return \Antares\Contracts\Extension\RouteGenerator
     */
    public function route($name, $default = '/')
    {
        if (!isset($this->routes[$name])) {
            $this->routes[$name] = $this->generateRouteByName($name, $default);
        }

        return $this->routes[$name];
    }

    /**
     * Run the callback when route is matched.
     *
     * @param  string  $path
     * @param  mixed   $listener
     *
     * @return void
     */
    public function when($path, $listener)
    {
        $listener = $this->app->make('events')->makeListener($listener);

        $this->app->booted(function () use ($listener, $path) {
            if ($this->is($path)) {
                call_user_func($listener);
            }
        });
    }

    /**
     * Generate route by name.
     *
     * @param  string  $name
     * @param  string  $default
     *
     * @return \Antares\Contracts\Extension\RouteGenerator
     */
    protected function generateRouteByName($name, $default)
    {
        return $this->app->make('antares.extension')->route($name, $default);
    }

    /**
     * Prepare valid route, since we already extract package from route
     * we can re-append query string to route value.
     *
     * @param  string  $route
     * @param  string  $item
     * @param  string  $query
     * @param  array   $options
     *
     * @return string
     */
    protected function prepareValidRoute($route, $item, $query, array $options)
    {
        if (!!Arr::get($options, 'csrf', false)) {
            $query .= (!empty($query) ? '&' : '' ) . '_token=' . $this->app->make('session')->token();
            unset($options['csrf']);
        }
        if (!!Arr::get($options, 'sandbox', false)) {
            $query .= (!empty($query) ? '&' : '' ) . 'sandbox=' . Arr::get($options, 'sandbox');
            unset($options['sandbox']);
        }
        if (!empty($options)) {
            if (isset($options['sandbox']) && $options['sandbox'] == false) {
                unset($options['sandbox']);
            }
            $query .= (!empty($query) ? '&' : '' ) . http_build_query($options);
        }
        !empty($item) && $route = "{$route}.{$item}";
        empty($route) && $route = '';
        empty($query) || $route = "{$route}?{$query}";


        return $route;
    }

}

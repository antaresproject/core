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
 * @package    UI
 * @version    0.9.2
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\Navigation\Breadcrumbs;

use Illuminate\Routing\Router;
use Exception;
use Illuminate\Support\Str;

class CurrentRoute {

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var array
     */
    protected $route;

    /**
     * CurrentRoute constructor.
     * @param Router $router
     */
    public function __construct(Router $router) {
        $this->router = $router;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function get() : array
    {
        if ($this->route) {
            return $this->route;
        }

        $route = $this->router->current();

        if($route === null) {
            return ['', []];
        }

        $name = $route->getName();
        $area = area();

        if($name === null) {
            return ['', []];
            $uri = head($route->methods()) . ' /' . $route->uri();
            throw new Exception("The current route ($uri) is not named - please check routes.php for an \"as\" parameter.");
        }
        elseif( Str::startsWith($name, $area . '.')) {
            $name = substr($name, strlen($area) + 1);
        }

        $params = array_values($route->parameters());

        return [$name, $params];
    }

    /**
     * @param string $name
     * @param array $params
     */
    public function set(string $name, array $params = []) : void {
        $this->route = [$name, $params];
    }

    public function clear() : void {
        $this->route = null;
    }

}
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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Http\Middleware;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Closure;

class Middleware
{

    /**
     * Application container
     *
     * @var Container 
     */
    protected $container;

    /**
     * Creates a new middleware instance.
     * 
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $action
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $action = null)
    {
        if ($this->container->make('antares.ui-components.installed') && $this->validateRequest($request) && !$request->ajax()) {
            $this->container->make('antares.ui-components')->detect();
        }
        return $next($request);
    }

    /**
     * Validates ajax request
     * 
     * @param Request $request
     * @return boolean
     */
    protected function validateRequest(Request $request)
    {
        if (php_sapi_name() === 'cli') {
            return false;
        }
        $route   = call_user_func($request->getRouteResolver());
        $ignored = $this->container->make('config')->get('antares/ui-components::ignore');
        $action  = $route->getAction();


        if (isset($action['uses']) and $action['uses'] instanceof Closure) {
            return true;
        }

        $resource = array_get($action, 'controller');

        foreach ($ignored as $controller => $actions) {
            foreach ($actions as $action) {
                if ($resource === "$controller@$action") {
                    return true;
                }
            }
        }

        return !( $request->method() !== 'GET' or ! empty($request->query()));
    }

}

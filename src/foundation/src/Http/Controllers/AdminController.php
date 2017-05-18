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

namespace Antares\Foundation\Http\Controllers;

use Illuminate\Support\Facades\Route;

abstract class AdminController extends BaseController
{

    /**
     * Base construct method.
     */
    public function __construct()
    {
        //$this->middleware('antares.installable');
        parent::__construct();
    }

    /**
     * Register middleware on the controller.
     *
     * @param  array|string  $middleware
     * @param  array   $options
     * @return \Illuminate\Routing\ControllerMiddlewareOptions
     */
    public function middleware($middleware, array $options = [])
    {
        if (!isset($options['only']) or ! app('antares.installed')) {
            return $this->runMiddleware($middleware, $options);
        }


        $route = Route::getCurrentRoute();
        if (!$route) {
            return;
        }

        $routeAction = $route->getAction();
        list($controller, $action) = explode('@', $routeAction['controller']);
        if (is_null($controller) or is_null($action)) {
            return $this->runMiddleware($middleware, $options);
        }

        $middlewareMatch = (is_array($options['only'])) ? in_array($action, $options['only']) : $action == $options['only'];
        if ($middlewareMatch) {
            $replacement = 'antares.can:';
            if (starts_with($middleware, $replacement)) {
                @list($component, $resource) = explode('::', preg_replace("/{$replacement}/", '', $middleware));
                if (is_null($resource)) {
                    $resource  = $component;
                    $component = 'antares/foundation';
                }
                app('antares.resource.repository')->add([
                    'component' => str_replace('/', '::', $component),
                    'resource'  => $resource,
                    'action'    => $action
                ]);
            }
        }
        return $this->runMiddleware($middleware, $options);
    }

    protected function runMiddleware($middleware, array $options = [])
    {
        parent::middleware($middleware, $options);
    }

}

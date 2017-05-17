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


namespace Antares\Foundation;

use Illuminate\Support\Facades\Route;
use ReflectionClass;

class Request
{

    /**
     * container with current route params
     *
     * @var array
     */
    protected $routeParams;

    /**
     * current controller name
     *
     * @var String 
     */
    protected static $controller;

    /**
     * current action name
     *
     * @var String
     */
    protected static $action;

    /**
     * currentmodule name
     *
     * @var String 
     */
    protected static $module;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->routeParams = !is_null($route             = Route::getCurrentRoute()) ? $route->getAction() : [];
    }

    /**
     * get current controller name by route
     * 
     * @return String
     */
    public function getController()
    {
        if (!is_null(self::$controller)) {
            return self::$controller;
        }
        $controllerParam = array_get($this->routeParams, 'controller');
        $match           = null;

        preg_match("'Foundation\\\Http\\\Controllers\\\(.*?)@'si", $controllerParam, $match);

        if (!isset($match[1]) && !preg_match("'Admin\\\(.*?)@'si", $controllerParam, $match)) {
            return;
        }
        self::$controller = str_replace('controller', '', strtolower($match[1]));
        return self::$controller;
    }

    /**
     * get controller class name
     * 
     * @return String
     */
    public function getControllerClass()
    {
        $controller = array_get($this->routeParams, 'controller');
        $match      = null;
        if (!preg_match('/(.*?)(?=@|$)/', $controller, $match)) {
            return;
        }
        return $match[0];
    }

    /**
     * get action name by route
     * 
     * @return String
     */
    public function getAction()
    {
        if (!is_null(self::$action)) {
            return self::$action;
        }
        $controllerParam = array_get($this->routeParams, 'controller');
        $match           = null;

        if (!preg_match("/.+?(?=@)@(.*)/", $controllerParam, $match)) {
            return;
        }
        self::$action = strtolower($match[1]);
        return self::$action;
    }

    /**
     * get module name by route
     * 
     * @return String
     */
    public function getModule()
    {
        if (!is_null(self::$module)) {
            return self::$module;
        }
        $controllerParam = array_get($this->routeParams, 'controller');
        $match           = null;
        preg_match('/(.*?)(?=@|$)/', $controllerParam, $match);
        if (!isset($match[0])) {
            return;
        }
        $reflection = new ReflectionClass($match[0]);
        $filename   = $reflection->getFileName();
        if (!preg_match("'antares(.*?)src'si", $filename, $match)) {
            return;
        }
        $reserved     = [
            'components', 'modules'
        ];
        self::$module = (str_contains($match[1], 'core')) ? 'core' : trim(str_replace($reserved, '', $match[1]), DIRECTORY_SEPARATOR);
        return self::$module;
    }

    /**
     * does the request wants api response
     * 
     * @return boolean
     */
    public function shouldMakeApiResponse()
    {
        $accept   = request()->header('Accept');
        $accepted = implode('.', [env('API_STANDARDS_TREE'), env('API_SUBTYPE')]);
        return request()->wantsJson() && extension_active('api') && str_contains($accept, $accepted) && class_exists('\Antares\Api\Http\Router\Adapter');
    }

}

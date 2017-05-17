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


namespace Antares\Foundation\Http\Resolver;

use Antares\Contracts\Http\Middleware\ModuleNamespaceResolver as ModuleResolverContract;
use Illuminate\Contracts\Routing\Registrar;
use RuntimeException;

class ModuleNamespaceResolver implements ModuleResolverContract
{

    /**
     * frontend default component content name
     */
    const frontendContentComponent = 'content';

    /**
     * map core names
     * 
     * @var String
     */
    private static $core = ['foundation', 'users'];

    /**
     * key registered for core
     *
     * @var String 
     */
    private static $registeredForCore = 'acl_antares';

    /**
     * name of namespace prefix
     * 
     * @var String
     */
    private static $namespacePrefix = 'antares';

    /**
     * @var \Illuminate\Routing\Route 
     */
    protected $route;

    /**
     * name of module 
     * 
     * @var String
     */
    protected $name;

    /**
     * instance creator
     * 
     * @param Registrar $registrar
     */
    public function __construct(Registrar $registrar)
    {
        $current = $registrar->current();
        if (!is_null($current)) {
            $this->route = $current->getAction();
        }
    }

    /**
     * module namespace resolver
     * 
     * @param array $matches
     * @return String
     * @throws RuntimeException
     */
    public function resolve($matches = [])
    {
        if (!isset($this->route['controller'])) {
            return false;
        }
        $name = $this->route['controller'];
        preg_match("/.+?(?=Http)/", $name, $matches);
        if (!isset($matches[0])) {
            throw new RuntimeException('Unable to resolve module namespace from controller.');
        }
        $this->name = str_replace('\\', '/', strtolower(rtrim($matches[0], '\\')));
        return $this->name;
    }

    /**
     * cleared namespace of module
     * 
     * @return String
     */
    public function getClear()
    {
        $return = str_replace(self::$namespacePrefix . '/', '', $this->name);
        $exists = array_where(self::$core, function($key) use($return) {
            return $return == $key;
        });
        if (!empty($exists)) {
            return self::$registeredForCore;
        }
        if ($return == 'app') {
            return 'content';
        }
        return ($return == 'app') ? self::frontendContentComponent : $return;
    }

    /*
     * resolve action name from route 
     * 
     * @return String
     */

    public function getAction($matches = [])
    {
        if (!isset($this->route['controller'])) {
            return false;
        }
        $controller = $this->route['controller'];
        preg_match("/@(.*)/", $controller, $matches);
        if (!isset($matches[1])) {
            throw new RuntimeException('Unable to resolve action name from controller.');
        }
        return $matches[1];
    }

    /**
     * resolver controller name from route
     * 
     * @param array $matches
     * @return boolean|String
     */
    public function getController($matches = [])
    {

        if (!isset($this->route['controller'])) {
            return false;
        }
        $controller = $this->route['controller'];
        preg_match("/.+?(?=Controllers)(.*)@/", $controller, $matches);
        if (!isset($matches[1])) {
            return false;
        }
        return str_ireplace(['Controllers', 'Controller', '\\'], "", $matches[1]);
    }

}

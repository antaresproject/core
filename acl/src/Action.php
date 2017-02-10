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


namespace Antares\Acl;

use Illuminate\Routing\Route;

class Action {
    
    /**
     *
     * @var string
     */
    protected $routeName;
    
    /**
     *
     * @var string
     */
    protected $action;
    
    /**
     * 
     * @param string $routeName
     * @param string $action
     */
    public function __construct($routeName, $action) {
        $this->routeName    = $routeName;
        $this->action       = $action;
    }
    
    /**
     * 
     * @return string
     */
    public function getRouteName() {
        return $this->routeName;
    }
    
    /**
     * 
     * @return string
     */
    public function getAction() {
        return $this->action;
    }
    
    /**
     * 
     * @return string
     */
    public function getActionAsParameter() {
        return str_slug($this->action);
    }
    
    /**
     * 
     * @param Route $route
     * @return bool
     */
    public function isMatchToRoute(Route $route) {
        return str_is($this->getRouteName(), $route->getName());
    }
    
}

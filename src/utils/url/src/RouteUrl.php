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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Url;

use Antares\Url\Permissions\CanHandler;
use Illuminate\Routing\Route;

class RouteUrl extends AbstractUrl {
    
    /**
     *
     * @var Route
     */
    protected $route;
    
    /**
     *
     * @var label
     */
    protected $label;
    
    private static $middlewareStartString = 'antares.can:';
    
    public function __construct(CanHandler $canHandler, Route $route, $label) {
        $this->route = $route;
        $this->label = $label;
        
        parent::__construct($canHandler, $this->getMatchedCanAction());
    }
    
    public function getUrl() {
        return $this->route->getPath();
    }

    public function getLabel() {
        return $this->label;
    }
    
    /**
     * 
     * @return string | null
     */
    protected function getMatchedCanAction() {
        $middlewares = $this->route->middleware();
        
        foreach($middlewares as $middleware) {
            if(starts_with($middleware, self::$middlewareStartString)) {
                return str_replace(self::$middlewareStartString, '', $middleware);
            }
        }
    }

}

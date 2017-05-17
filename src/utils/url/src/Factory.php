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
use Illuminate\Routing\Router;
use Illuminate\Http\Request;

class Factory {
    
    /**
     *
     * @var CanHandler 
     */
    protected $canHandler;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Request
     */
    protected $request;
    
    /**
     * 
     * @param CanHandler $canHandler
     */
    public function __construct(CanHandler $canHandler, Router $router, Request $request) {
        $this->canHandler   = $canHandler;
        $this->router       = $router;
        $this->request      = $request;
    }
    
    /**
     * 
     * @param string $url
     * @param string $label
     * @return \Antares\Url\RouteUrl
     */
    public function makeUrl($url, $label) {
        $route = $this->router->getRoutes()->match($this->request->create($url));

        return new RouteUrl($this->canHandler, $route, $label);
    }
    
}

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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */





namespace Antares\Tester;

use Illuminate\Http\Request;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Contracts\Container\Container as IlluminateContainer;

class Dispatcher
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Router instance.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Construct a new Resources instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $app
     * @param  \Illuminate\Routing\Router  $router
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(IlluminateContainer $app, IlluminateRouter $router, Request $request)
    {
        $this->app     = $app;
        $this->router  = $router;
        $this->request = $request;
    }

}

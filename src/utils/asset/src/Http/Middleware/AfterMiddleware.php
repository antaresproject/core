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


namespace Antares\Asset\Http\Middleware;

use Antares\Foundation\Application;
use Closure;

class AfterMiddleware
{

    /**
     * application instance
     *
     * @var Application
     */
    protected $app;

    /**
     * constructing
     * 
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * middleware handler
     * 
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (!isset($this->app['assetic.options']['auto_dump_assets']) || !$this->app['assetic.options']['auto_dump_assets']) {
            return $response;
        }
        $helper = $this->app['assetic.dumper'];
        if (isset($this->app['twig'])) {
            $helper->addTwigAssets();
        }
        $helper->dumpAssets();
        return $response;
    }

}

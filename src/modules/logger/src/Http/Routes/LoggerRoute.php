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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Http\Routes;

use Illuminate\Contracts\Routing\Registrar;
use Closure;

class LoggerRoute
{
    /* ------------------------------------------------------------------------------------------------
      |  Properties
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * The router instance.
     *
     * @var Registrar
     */
    protected $router;

    /* ------------------------------------------------------------------------------------------------
      |  Route registration Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Register a new GET route with the router.
     *
     * @param  string                $uri
     * @param  Closure|array|string  $action
     */
    public function get($uri, $action)
    {
        $this->router->get($uri, $action);
    }

    /**
     * Register and map routes.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     */
    public static function register(Registrar $router)
    {
        (new static)->setRegister($router)->map($router);
    }

    /* ------------------------------------------------------------------------------------------------
      |  Getter & Setters
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * @param  Registrar  $router
     *
     * @return self
     */
    public function setRegister(Registrar $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Map routes.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     */
    public function map(Registrar $router)
    {
        $this->group(['prefix' => 'logger/details',], function() {
            $this->registerSingleLogRoutes();
        });
    }

    /**
     * Register single log routes.
     */
    private function registerSingleLogRoutes()
    {
        $this->group(['prefix' => '{date}',], function() {
            $this->get('{level}', [
                'as'   => 'logger::logs.filter',
                'uses' => 'IndexController@details',
            ]);
        });
    }

    /**
     * Create a route group with shared attributes.
     *
     * @param  array    $attributes
     * @param  Closure  $callback
     */
    protected function group(array $attributes, Closure $callback)
    {
        $this->router->group($attributes, $callback);
    }

}

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


namespace Antares\Users\Http\Middleware;

use Illuminate\Contracts\Routing\ResponseFactory;
use Antares\Contracts\Foundation\Foundation;
use Illuminate\Contracts\Config\Repository;
use Antares\Url\Permissions\CanHandler;
use Antares\Contracts\Auth\Guard;
use Closure;

class Can
{

    /**
     * The application implementation.
     *
     * @var Antares\Contracts\Foundation\Foundation
     */
    protected $foundation;

    /**
     *
     * @var CanHandler
     */
    protected $canHandler;

    /**
     * The authenticator implementation.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * The config repository implementation.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The response factory implementation.
     *
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $response;

    /**
     * module action delimiter
     *
     * @var String
     */
    private static $delimiter = '::';

    /**
     * Create a new filter instance.
     *
     * @param  Antares\Contracts\Foundation\Foundation  $foundation
     * @param  CanHandler $canHandler
     * @param  \Antares\Contracts\Auth\Guard  $auth
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @param  \Illuminate\Contracts\Routing\ResponseFactory  $response
     */
    public function __construct(Foundation $foundation, CanHandler $canHandler, Guard $auth, Repository $config, ResponseFactory $response)
    {
        $this->foundation = $foundation;
        $this->canHandler = $canHandler;
        $this->auth       = $auth;
        $this->config     = $config;
        $this->response   = $response;
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
    public function handle($request, Closure $next, $action = null)
    {
        if (!$this->authorize($action)) {
            return $this->responseOnUnauthorized($request);
        }
        return $next($request);
    }

    /**
     * Check authorization.
     *
     * @param  string  $action
     *
     * @return bool
     */
    protected function authorize($action = null)
    {
        return $this->canHandler->canAuthorize($action);
    }

    /**
     * Response on authorized request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return mixed
     */
    protected function responseOnUnauthorized($request)
    {
        if ($request->ajax()) {
            return $this->response->make('Unauthorized', 401);
        }
        return redirect_with_message(handles('antares/foundation::/'), trans('You are not allowed to perform previous action.'), 'error');
    }

}

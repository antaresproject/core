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

use Closure;
use Antares\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Antares\Contracts\Authorization\Authorization;

class LoginAs
{

    /**
     * The authorization implementation.
     *
     * @var \Antares\Contracts\Authorization\Authorization
     */
    protected $acl;

    /**
     * The authentication implementation.
     *
     * @var \Antares\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Construct a new middleware.
     *
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @param  \Antares\Contracts\Auth\Guard  $auth
     */
    public function __construct(Authorization $acl, Guard $auth)
    {
        $this->acl  = $acl;
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $as = $request->input('_as');

        if ($this->authorize() && !is_null($as)) {
            $this->auth->loginUsingId($as);

            return new RedirectResponse($request->url());
        }

        return $next($request);
    }

    /**
     * Check authorization.
     *
     * @return bool
     */
    protected function authorize()
    {
        return $this->acl->can('manage antares');
    }

}

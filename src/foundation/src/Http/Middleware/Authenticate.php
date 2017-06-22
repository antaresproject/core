<?php

namespace Antares\Foundation\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class Authenticate
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() OR $request->wantsJson()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(handles('antares::login'));
            }
        }

        return $next($request);
    }

}

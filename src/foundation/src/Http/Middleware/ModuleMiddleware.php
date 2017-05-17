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


namespace Antares\Foundation\Http\Middleware;

use Closure;
use Antares\Contracts\Authorization\Factory;
use Antares\Contracts\Http\Middleware\ModuleNamespaceResolver;

class ModuleMiddleware
{

    /**
     * Create a new middleware instance.
     * 
     * @param Factory $acl
     */
    public function __construct(Factory $acl, ModuleNamespaceResolver $resolver)
    {
        $namespace = $resolver->resolve();
        $this->acl = $acl->make($namespace);
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
        return ($this->acl->can($action)) ? $next($request) : redirect_with_message(handles('admin'), trans('antares/foundation::response.acl.not-allowed'));
    }

}

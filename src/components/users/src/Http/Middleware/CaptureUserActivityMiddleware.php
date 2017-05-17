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

namespace Antares\Users\Http\Middleware;

use Antares\Users\Processor\Activity\UsersActivity;
use Closure;
use Illuminate\Http\Request;

class CaptureUserActivityMiddleware
{

    /** @var  UsersActivity */
    protected $processor;

    /**
     * CaptureUserActivityMiddleware constructor.
     * @param UsersActivity $processor
     */
    public function __construct(UsersActivity $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('userActivity')) {
            $this->processor->updateActivity(auth()->user());
            exit();
        }

        return $next($request);
    }

}

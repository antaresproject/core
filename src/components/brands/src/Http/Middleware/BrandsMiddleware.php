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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Brands\Http\Middleware;

use Illuminate\Support\Facades\Crypt;
use Antares\Brands\Model\Brands;
use Illuminate\Http\Request;
use Urlcrypt\Urlcrypt;
use Closure;

class BrandsMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->guest() or ( $request->segment(2) == 'logout' && $request->segment(3) == 'with')) {
            return $next($request);
        }
        if (!user()->hasRoles(['member'])) {
            return $next($request);
        }

        if ((int) Brands::query()->findOrFail(brand_id())->options->maintenance) {
            $key = app('multiuser')->getKey();
            return response(view('antares/brands::maintenance', ['key' => $key]), 404);
        }
        return $next($request);
    }

}

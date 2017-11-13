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

namespace Antares\Area\Middleware;

use Antares\Area\Contracts\AreaManagerContract;
use Illuminate\Http\Request;
use Closure;

class AreaMiddleware
{

    /**
     * @var AreaManagerContract
     */
    protected $areaManager;

    /**
     * AreaMiddleware constructor.
     * @param AreaManagerContract $areaManager
     */
    public function __construct(AreaManagerContract $areaManager)
    {
        $this->areaManager = $areaManager;
    }

    /**
     * Checking whther user is allowed to area
     * 
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->areaManager->hasAreaInUri()) {
            return $next($request);
        }

        $area = $this->areaManager->getCurrentArea()->getId();

        if (auth()->check() && $area && $area !== 'antares' && !$request->ajax() && !($request->isJson() OR $request->wantsJson())) {
            $areas = user()->roles->pluck('area')->toArray();

            if (!in_array($area, $areas)) {
                return redirect_with_message(handles('antares/foundation::/'), trans('You are not allowed to area.'), 'error');
            }
        }

        return $next($request);
    }

}

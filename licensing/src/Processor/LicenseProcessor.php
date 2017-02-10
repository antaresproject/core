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


namespace Antares\Licensing\Processor;

use Antares\Licensing\Contracts\LicenseListener;
use Antares\Foundation\Processor\Processor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseProcessor extends Processor
{

    /**
     * realize index action page controller
     * 
     * @param LicenseListener $listener
     * @param Request $request
     * @return JsonResponse|\Illuminate\View\View
     */
    public function index(LicenseListener $listener, Request $request)
    {
        $license = app('antares.license')->validate();

        app('Antares\Contracts\Auth\Guard')->logout();

        if ($request->ajax()) {
            return new JsonResponse($license);
        }
        return $listener->invalid($license);
    }

}

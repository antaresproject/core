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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Http\Controllers\Extension;

use Illuminate\Support\Fluent;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Contracts\Extension\Listener\Extension;

abstract class Controller extends AdminController implements Extension
{

    /**
     * Abort request when extension requirement mismatched.
     *
     * @return mixed
     */
    public function abortWhenRequirementMismatched()
    {
        return $this->suspend(404);
    }

    /**
     * Get extension information.
     *
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function getExtension($vendor, $package = null)
    {
        $name = (is_null($package) ? $vendor : implode('/', [$vendor, $package]));
        return new Fluent(['name' => $name, 'uid' => $name]);
    }

}

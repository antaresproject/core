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
 namespace Antares\Contracts\Extension;

interface RouteGenerator
{
    /**
     * Get route domain.
     *
     * @param  bool  $forceBase
     *
     * @return string
     */
    public function domain($forceBase = false);

    /**
     * Determine if the current request URI matches a pattern.
     *
     * @param  string  $pattern
     *
     * @return bool
     */
    public function is($pattern);

    /**
     * Get the current path info for the request.
     *
     * @return string
     */
    public function path();

    /**
     * Get route prefix.
     *
     * @param  bool  $forceBase
     *
     * @return string
     */
    public function prefix($forceBase = false);

    /**
     * Get route root.
     *
     * @return string
     */
    public function root();

    /**
     * Get route to.
     *
     * @param  string  $to
     *
     * @return string
     */
    public function to($to);
}

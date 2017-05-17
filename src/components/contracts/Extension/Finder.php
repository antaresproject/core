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

interface Finder
{
    /**
     * Add a new path to finder.
     *
     * @param  string  $path
     *
     * @return $this
     */
    public function addPath($path);

    /**
     * Detect available extensions.
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function detect();

    /**
     * Register the extension.
     *
     * @param  string  $name
     * @param  string  $path
     *
     * @return bool
     */
    public function registerExtension($name, $path);
}

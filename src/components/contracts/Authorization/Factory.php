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
 namespace Antares\Contracts\Authorization;

use Antares\Contracts\Memory\Provider;

interface Factory
{
    /**
     * Initiate a new ACL Container instance.
     *
     * @param  string  $name
     * @param  \Antares\Contracts\Memory\Provider  $memory
     *
     * @return \Antares\Contracts\Authorization\Authorization
     */
    public function make($name = null, Provider $memory = null);

    /**
     * Register an ACL Container instance with Closure.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return \Antares\Contracts\Authorization\Authorization
     */
    public function register($name, $callback = null);

    /**
     * Shutdown/finish all ACL.
     *
     * @return $this
     */
    public function finish();

    /**
     * Get all ACL instances.
     *
     * @return array
     */
    public function all();

    /**
     * Get ACL instance by name.
     *
     * @param  string  $name
     *
     * @return \Antares\Contracts\Authorization\Authorization
     */
    public function get($name);
}

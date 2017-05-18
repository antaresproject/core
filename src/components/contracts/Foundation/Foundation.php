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

namespace Antares\Contracts\Foundation;

use Closure;
use Antares\Contracts\Http\RouteManager;

interface Foundation extends RouteManager
{

    /**
     * Start the application.
     *
     * @return $this
     */
    public function boot();

    /**
     * Get installation status.
     *
     * @return bool
     */
    public function installed();

    /**
     * Get acl services.
     *
     * @var \Antares\Contracts\Auth\Authorization
     */
    public function acl();

    /**
     * Get memory services.
     *
     * @var \Antares\Contracts\Memory\Provider
     */
    public function memory();

    /**
     * Get menu services.
     *
     * @var \Antares\UI\TemplateBase\Menu
     */
    public function menu();

    /**
     * Register the given Closure with the "group" function namespace set.
     *
     * @param  string|null  $namespace
     * @param  \Closure|null  $callback
     *
     * @return void
     */
    public function namespaced($namespace, Closure $callback);
}

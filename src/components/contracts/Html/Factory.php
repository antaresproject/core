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
 namespace Antares\Contracts\Html;

use Closure;

interface Factory
{
    /**
     * Create a new Builder instance.
     *
     * @param  \Closure|null    $callback
     *
     * @return \Antares\Contracts\Html\Builder
     */
    public function make(Closure $callback = null);

    /**
     * Create a new builder instance of a named builder.
     *
     * @param  string   $name
     * @param  \Closure $callback
     *
     * @return \Antares\Contracts\Html\Builder
     */
    public function of($name, Closure $callback = null);
}

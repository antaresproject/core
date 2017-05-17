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
 namespace Antares\Contracts\Extension\Command;

use Illuminate\Support\Fluent;
use Antares\Contracts\Extension\Listener\Configure as Listener;

interface Configure
{
    /**
     * View edit extension configuration page.
     *
     * @param  \Antares\Contracts\Extension\Listener\Configure  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function configure(Listener $listener, Fluent $extension);

    /**
     * Update an extension configuration.
     *
     * @param  \Antares\Contracts\Extension\Listener\Configure  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(Listener $listener, Fluent $extension, array $input);
}

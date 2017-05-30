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
 namespace Antares\Contracts\Messages;

use Closure;
use Illuminate\Contracts\Support\MessageBag as MessageBagContract;

interface MessageBag extends MessageBagContract
{
    /**
     * Extend Messages instance from session.
     *
     * @param  \Closure  $callback
     *
     * @return static
     */
    public function extend(Closure $callback);

    /**
     * Retrieve Message instance from Session, the data should be in
     * serialize, so we need to unserialize it first.
     *
     * @return $this
     */
    public function retrieve();

    /**
     * Store current instance.
     *
     * @return void
     */
    public function save();

    /**
     * Compile the instance into serialize.
     *
     * @return string  serialize of this instance
     */
    public function serialize();
}

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
 namespace Antares\Contracts\Notification;

use Closure;

interface Notification
{
    /**
     * Send notification via API.
     *
     * @param  \Antares\Contracts\Notification\Recipient  $user
     * @param  \Antares\Contracts\Notification\Message  $message
     * @param  \Closure|null  $callback
     *
     * @return \Antares\Contracts\Notification\Receipt
     */
    public function send(Recipient $user, Message $message, Closure $callback = null);
}

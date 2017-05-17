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

interface Receipt
{
    /**
     * Return true when all e-mail has been sent.
     *
     * @return bool
     */
    public function sent();

    /**
     * Return true if any of the e-mail failed to be sent.
     *
     * @return bool
     */
    public function failed();

    /**
     * Get list of failed email recipient.
     *
     * @return array
     */
    public function failures();

    /**
     * Set whether or not e-mail is sent via queue/delayed.
     *
     * @param  bool  $usingQueue
     *
     * @return $this
     */
    public function usingQueue($usingQueue = false);

    /**
     * Get if e-mail is queued/delayed.
     *
     * @return bool
     */
    public function isQueued();
}

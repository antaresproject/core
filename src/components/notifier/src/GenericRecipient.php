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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Notifier;

use Antares\Contracts\Notification\Recipient;

class GenericRecipient implements Recipient
{
    /**
     * Recipient e-mail address.
     *
     * @var string
     */
    protected $email;

    /**
     * Recipient name.
     *
     * @var string
     */
    protected $name;

    /**
     * Create a new recipient.
     *
     * @param  string  $email
     * @param  string  $name
     */
    public function __construct($email, $name)
    {
        $this->email = $email;
        $this->name  = $name;
    }

    /**
     * Get the e-mail address where notification are sent.
     *
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->email;
    }

    /**
     * Get the fullname where notification are sent.
     *
     * @return string
     */
    public function getRecipientName()
    {
        return $this->name;
    }
}

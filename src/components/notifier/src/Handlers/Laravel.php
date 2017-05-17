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


namespace Antares\Notifier\Handlers;

use Closure;
use Illuminate\Mail\Message;
use Antares\Notifier\Receipt;
use Illuminate\Contracts\Mail\Mailer as Mail;
use Antares\Contracts\Notification\Recipient;
use Antares\Contracts\Notification\Notification;
use Antares\Contracts\Notification\Message as MessageContract;

class Laravel implements Notification
{

    /**
     * Mailer instance.
     *
     * @var \Illuminate\Contracts\Mail\Mailer
     */
    protected $mailer;

    /**
     * Setup Illuminate Mailer.
     *
     * @param  \Illuminate\Contracts\Mail\Mailer  $mailer
     */
    public function __construct(Mail $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send notification via API.
     *
     * @param  \Antares\Contracts\Notification\Recipient  $user
     * @param  \Antares\Contracts\Notification\Message  $message
     * @param  \Closure  $callback
     *
     * @return \Antares\Contracts\Notification\Receipt
     */
    public function send(Recipient $user, MessageContract $message, Closure $callback = null)
    {
        $view    = $message->getView();
        $data    = $message->getData();
        $subject = $message->getSubject();

        $this->mailer->send($view, $data, function (Message $mail) use ($user, $subject, $callback) {
            $mail->to($user->getRecipientEmail(), $user->getRecipientName());

            !empty($subject) && $mail->subject($subject);

            is_callable($callback) && call_user_func_array($callback, func_get_args());
        });

        return new Receipt($this->mailer, false);
    }

}

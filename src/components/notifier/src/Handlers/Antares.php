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

use Antares\Contracts\Notification\Message as MessageContract;
use Antares\Contracts\Notification\Notification;
use Antares\Contracts\Notification\Recipient;
use Antares\Contracts\Memory\Provider;
use SuperClosure\SerializableClosure;
use Antares\Memory\ContainerTrait;
use Illuminate\Mail\Message;
use Antares\Notifier\Mailer;
use Closure;

class Antares implements Notification
{

    use ContainerTrait;

    /**
     * Mailer instance.
     *
     * @var Mailer
     */
    protected $mailer;

    /**
     * Construct a new Antares notifier.
     *
     * @param  Mailer  $mailer
     */
    public function __construct(Mailer $mailer)
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
        $view     = $message->getView();
        $data     = $message->getData() ? : [];
        $subject  = $message->getSubject() ? : '';
        $callback = ($callback instanceof Closure ? new SerializableClosure($callback) : $callback);
        $receipt  = $this->mailer->send(view($view, $data)->render(), $data, function (Message $message) use ($user, $subject, $callback) {
            $message->to($user->getRecipientEmail(), $user->getRecipientName());
            !empty($subject) && $message->subject($subject);
            is_callable($callback) && call_user_func_array($callback, func_get_args());
        });

        return $receipt->usingQueue($this->isUsingQueue());
    }

    /**
     * Determine if mailer using queue.
     *
     * @return bool
     */
    protected function isUsingQueue()
    {

        $usingQueue = false;
        $usingApi   = 'mail';

        if ($this->memory instanceof Provider) {
            $usingQueue = $this->memory->get('email.queue', false);
            $usingApi   = $this->memory->get('email.driver');
        }

        return ($usingQueue || in_array($usingApi, ['mailgun', 'mandrill', 'log']));
    }

}

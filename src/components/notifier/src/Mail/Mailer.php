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

namespace Antares\Notifier\Mail;

use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Mail\Mailer as SupportMailer;

class Mailer extends SupportMailer
{

    /**
     * Send a new message using a view.
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @return void
     */
    public function send($view, array $data = [], $callback = null)
    {

        if ($view instanceof MailableContract) {
            return $this->sendMailable($view);
        }
        list($view, $plain, $raw) = $this->parseView($view);
        $this->from      = ['address' => 'lukasz.cirut@gmail.com', 'name' => 'Lukasz Cirut'];
        $data['message'] = $message         = $this->createMessage();
        $this->addContent($message, $view, $plain, $raw, $data);

        call_user_func($callback, $message);

        if (isset($this->to['address'])) {
            $this->setGlobalTo($message);
        }

        $swiftMessage = $message->getSwiftMessage();

        if ($this->shouldSendMessage($swiftMessage)) {
            $this->sendSwiftMessage($swiftMessage);

            $this->dispatchSentEvent($message);
        }
    }

}

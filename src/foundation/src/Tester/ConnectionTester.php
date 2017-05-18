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

namespace Antares\Foundation\Tester;

use Antares\Tester\Adapter\ResponseAdapter;
use Swift_SmtpTransport as SmtpTransport;
use Antares\Tester\Contracts\Tester;
use Illuminate\Mail\Message;
use Swift_Message;
use Swift_Mailer;
use Exception;

class ConnectionTester extends ResponseAdapter implements Tester
{

    /**
     * playground configuration validator
     * 
     * @param array $data
     * @return \Antares\Playground\Tester\PlaygroundTester
     */
    public function __invoke(array $data = null)
    {
        $driver = array_get($data, 'email_driver');
        if ($driver === 'sendmail') {
            $this->sendmail($data);
        }
        if ($driver === 'smtp') {
            try {
                $this->smtp($data);
            } catch (Exception $ex) {
                $this->setError('UNABLE_TO_SEND_SMTP_EMAIL');
            }
        }
        return $this;
    }

    /**
     * Checks smtp connection
     * 
     * @param array $data
     * @return $this
     */
    protected function smtp(array $data = null)
    {
        $valid = true;
        foreach (['email_address', 'email_host', 'email_port', 'email_username', 'email_password', 'email_encryption'] as $key) {

            if (!strlen(array_get($data, $key))) {
                break;
                $valid = false;
            }
        }
        if (!$valid) {
            $this->setError('INVALID_SETTINGS');
        }
        // The Swift SMTP transport instance will allow us to use any SMTP backend
        // for delivering mail such as Sendgrid, Amazon SES, or a custom server
        // a developer has available. We will just pass this configured host.
        $transport = SmtpTransport::newInstance(
                        $data['email_host'], $data['email_port']
        );

        if (isset($data['email_encryption'])) {
            $transport->setEncryption($data['email_encryption']);
        }

        // Once we have the transport we will check for the presence of a username
        // and password. If we have it we will set the credentials on the Swift
        // transporter instance so that we'll properly authenticate delivery.
        if (isset($data['email_username'])) {
            $transport->setUsername($data['email_username']);
            $transport->setPassword($data['email_password']);
        }
        $mailer = new Swift_Mailer($transport);


        $message = new Message(new Swift_Message);


        $message->from($data['email_address'], $data['email_address']);
        $message->setBody(trans('antares/foundation::tester.smtp_connection_succeed'), 'text/html');
        $message->setTo(user()->email);
        $message->setSubject(trans('antares/foundation::tester.email_title'));
        $failedRecipients = [];
        $mailer->send($message->getSwiftMessage(), $failedRecipients);
        if (!empty($failedRecipients)) {
            $this->setError('UNABLE_TO_SEND_SMTP_EMAIL');
        } else {
            $this->addSuccess(trans('antares/foundation::tester.smtp_connection_succeed'));
        }
        return $this;
    }

    /**
     * Checks sendmail configuration
     * 
     * @param array $data
     * @return $this
     */
    protected function sendmail(array $data = null)
    {
        $this->setError('TEST_NOT_SUPPORTED');
        return $this;
    }

}

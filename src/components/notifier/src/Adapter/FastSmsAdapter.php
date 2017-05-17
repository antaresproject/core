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


namespace Antares\Notifier\Adapter;

use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Antares\Notifier\Message\SmsMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Message;
use Illuminate\View\View;
use Exception;
use Closure;

class FastSmsAdapter extends AbstractAdapter
{

    /**
     * sends sms message
     * 
     * @param View|String $view
     * @param array $data
     * @param closure $callback
     * @return boolean
     */
    public function send($view, array $data, $callback)
    {
        $message = $this->getMessage();
        $this->callMessageBuilder($callback, $message);
        $message->content($view, $data);
        if ($message->validate() and $this->validate()) {
            return $this->push($message);
        }
        return false;
    }

    /**
     * Call the provided message builder.
     *
     * @param  \Closure|string  $callback
     * @param  Message  $message
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    protected function callMessageBuilder($callback, $message)
    {
        if ($callback instanceof Closure) {
            return call_user_func($callback, $message);
        }
        throw new InvalidArgumentException('Callback is not valid.');
    }

    /**
     * sends sms message
     * 
     * @param String $message
     * @param String $recipient
     * @return mixed
     * @throws Exception
     */
    protected function push($message)
    {
        $addresses = $message->getTo();
        $body      = $message->getContent();
        $params    = array(
            'Body'           => $body,
            'ValidityPeriod' => '86400'
        );
        if (is_array($addresses)) {
            $result               = null;
            $shouldThrowException = false;
            foreach ($addresses as $address) {
                $result = $this->process($params, $address, true);
                if (isset($this->config['codes'][$result]) && !$shouldThrowException) {
                    $shouldThrowException = true;
                }
            }
            return $result;
        } else {
            return $this->process($params, $message->getTo());
        }
    }

    /**
     * process sending
     * 
     * @param array $params
     * @param Sring $address
     * @param boolean $isMultiple
     * @return mixed
     * @throws Exception
     */
    protected function process($params, $address, $isMultiple = false)
    {
        $params['DestinationAddress'] = $address;
        $result                       = $this->request('Send', $params);
        if (!isset($this->config['codes'][$result])) {
            return $result;
        }
        $this->setResultCode($result);
        $this->setResultMessage($this->config['codes'][$result]);
        $message = sprintf('Unable to send sms to %s with content: %s. Provider error message: %s.', $address, $params['Body'], $this->config['codes'][$result]);
        Log::alert($message);
        return false;
    }

    /**
     * validates whether connection to gateway is established
     * 
     * @return type
     * @throws Exception
     */
    protected function validate()
    {
        $result = $this->request($action = 'CheckCredits');
        if (isset($this->config['codes'][$result])) {
            throw new Exception($this->config['codes'][$result]);
        }
        return $result;
    }

    /**
     * get message instance
     * 
     * @return SmsMessage
     */
    protected function getMessage()
    {
        return app()->make(SmsMessage::class);
    }

    /**
     * create request to sms gateway
     * 
     * @param String $action
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    protected function request($action = '', $params = array())
    {
        $params['Token'] = $this->config['api']['token'];
        if ($action !== '') {
            $params['Action'] = $action;
        }

        $query = http_build_query($params);
        $url   = $this->config['api']['url'];
        $ch    = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);
        curl_close($ch);
        if (!$result) {

            throw new Exception('Connection failed. Reason unknown.');
        }

        return $result;
    }

}

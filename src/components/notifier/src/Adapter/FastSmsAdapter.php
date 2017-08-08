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

use Antares\Notifications\Messages\SmsMessage;
use Exception;

class FastSmsAdapter extends AbstractAdapter
{

    /**
     * Sends sms message
     * 
     * @param SmsMessage $message
     * @param type $to
     * @return boolean
     */
    public function send(SmsMessage $message, $to)
    {

        $text = trim($message->content);
        if ($this->validate()) {
            return $this->push($to, $text);
        }

        return false;
    }

    /**
     * Sends sms message
     * 
     * @param String $from
     * @param mixed $to
     * @param String $text
     * @return mixed
     * @throws Exception
     */
    protected function push($to, $text)
    {
        $params = [
            'Body'           => $text,
            'ValidityPeriod' => '86400'
        ];
        if (is_array($to)) {
            $result = [];
            foreach ($to as $address) {
                array_push($result, $this->process($params, $address, true));
            }
            return $result;
        }
        return $this->process($params, $to);
    }

    /**
     * Process sending
     * 
     * @param array $params
     * @param Sring $to
     * @return mixed
     * @throws Exception
     */
    protected function process($params, $to)
    {
        $params['DestinationAddress'] = $to;
        $result                       = $this->request('Send', $params);
        if (!isset($this->config['codes'][$result])) {
            return $result;
        }
        $this->setResultCode($result);
        $this->setResultMessage($this->config['codes'][$result]);
        throw new Exception(sprintf('Unable to send sms to %s with content: %s. Provider error message: %s.', $to, $params['Body'], $this->config['codes'][$result]));
    }

    /**
     * Validates whether connection to gateway is established
     * 
     * @return mixed
     * @throws Exception
     */
    protected function validate()
    {
        $result = $this->request($action = 'CheckCredits');
        if ((int) $result <= 0) {
            throw new Exception($this->config['codes']['-100']);
        }
        return $result;
    }

    /**
     * Creates request to sms gateway
     * 
     * @param String $action
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    protected function request($action = '', array $params = [])
    {

        $params['Token'] = $this->config['api']['token'];
        if ($action !== '') {
            $params['Action'] = $action;
        }

        $query = http_build_query($params);
        $url   = $this->config['api']['url'];

        $ch = curl_init();

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

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

use Antares\Contracts\Notification\Recipient;

abstract class AbstractAdapter
{

    /**
     * adapter configuration container
     *
     * @var array 
     */
    protected $config;

    /**
     * Result code
     *
     * @var mixed
     */
    protected $code = null;

    /**
     * Result message
     *
     * @var String
     */
    protected $message = null;

    /**
     * constructing
     * 
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * validates whether connection to gateway is established
     */
    abstract protected function validate();

    /**
     * create request to sms gateway
     */
    abstract protected function request($action = '', $params = array());

    /**
     * Sets result code
     * 
     * @param mixed $code
     * @return \Antares\Notifier\Adapter\AbstractAdapter
     */
    protected function setResultCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Sets result message
     * 
     * @param String $message
     * @return \Antares\Notifier\Adapter\AbstractAdapter
     */
    protected function setResultMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Result message getter
     * 
     * @return String
     */
    public function getResultMessage()
    {
        return $this->message;
    }

    /**
     * Result code getter
     * 
     * @return mixed
     */
    public function getResultCode()
    {
        return $this->code;
    }

}

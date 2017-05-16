<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Adapter;

class MessageAdapter
{

    /**
     * name of default messages key
     */
    const DEFAULT_DOMAIN = 'default';

    /**
     * @var array 
     */
    private $config;

    /**
     * @var array 
     */
    protected $messages = [];

    /**
     * @var array 
     */
    private $codes;

    /**
     * default domain of error codes
     * 
     * @var String
     */
    protected $domain = 'default';

    /**
     * constructing
     */
    public function __construct()
    {
        $this->config = config('antares/tester::codes.errors', []) + config('tester.codes.errors', []);
    }

    /**
     * description getter
     * 
     * @param String $descriptor
     * @return String
     */
    public function getDescription($descriptor)
    {
        return $this->codes['descriptions'][$this->getCode($descriptor)];
    }

    /**
     * code getter
     * 
     * @param String $descriptor
     * @return numeric
     */
    public function getCode($descriptor)
    {
        return $this->codes['codes'][$descriptor];
    }

    /**
     * add message to container;
     * 
     * @param String $message
     * @param numeric $code
     * @param String $type
     * @return \Antares\Tester\Adapter\ResponseAdapter
     */
    public function add($message, $code, $type)
    {
        $default = ['message' => $message, 'code' => $code, 'type' => $type];
        if ($type == 'error' && is_numeric($code) && $code >= 0) {
            $default['descriptor'] = array_search($code, $this->codes['codes']);
        }
        if ($this->domain !== self::DEFAULT_DOMAIN) {
            $default['domain'] = $this->domain;
        }
        array_push($this->messages, $default);
        return $this;
    }

    /**
     * domain setter
     * 
     * @param String $domain
     * @return \Antares\Tester\Adapter\ResponseAdapter
     */
    public function setDomain($domain = self::DEFAULT_DOMAIN)
    {
        $this->domain = $domain;
        if (isset($this->config[$this->domain]) && !empty($this->config[$this->domain])) {
            $this->codes = $this->config[$this->domain];
        } else {
            $this->codes  = $this->config[self::DEFAULT_DOMAIN];
            $this->domain = self::DEFAULT_DOMAIN;
        }
        return $this;
    }

    /**
     * retrieve all messages
     * 
     * @return array
     */
    public function messages()
    {
        return $this->messages;
    }

}

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

use Antares\Tester\Contracts\Response as ResponseContract;
use Antares\Tester\Adapter\MessageAdapter;

class ResponseAdapter implements ResponseContract
{

    /**
     * @var \Antares\Tester\Adapter\ResponseAdapter 
     */
    protected $adapter;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->adapter = new MessageAdapter();
    }

    /**
     * add error into container
     * 
     * @param String $message
     * @param numeric $code
     * @return \Antares\Tester\Factory\ResponseFactory
     */
    protected function addError($message, $code = null)
    {
        $this->adapter->add($message, $code, 'error');
        return $this;
    }

    /**
     * add warning into container
     * 
     * @param String $message
     * @param numeric $code
     * @return \Antares\Tester\Factory\ResponseFactory
     */
    protected function addWarning($message, $code = null)
    {
        $this->adapter->add($message, $code, 'success');
        return $this;
    }

    /**
     * add info into container
     * 
     * @param String $message
     * @param numeric $code
     * @return \Antares\Tester\Factory\ResponseFactory
     */
    protected function addInfo($message, $code = null)
    {
        $this->adapter->add($message, $code, 'info');
        return $this;
    }

    /**
     * add success into container
     * 
     * @param String $message
     * @param numeric $code
     * @return \Antares\Tester\Factory\ResponseFactory
     */
    protected function addSuccess($message, $code = null)
    {
        $this->adapter->add($message, $code, 'success');
        return $this;
    }

    /**
     * retrive all messages in container
     * 
     * @return array
     */
    public function getResponse()
    {
        return $this->adapter->messages();
    }

    /**
     * error etter
     * 
     * @param String $descriptor
     * @param String $domain
     * @return \Antares\Tester\Factory\ResponseFactory
     */
    public function setError($descriptor, $domain = null)
    {
        $this->adapter->setDomain($domain);
        $this->addError($this->adapter->getDescription($descriptor), $this->adapter->getCode($descriptor));
        return $this;
    }

}

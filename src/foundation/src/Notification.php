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


namespace Antares\Foundation;

class Notification
{

    /**
     * singleton instance
     *
     * @var Notification
     */
    private static $instance;

    /**
     * params container
     * 
     * @var array
     */
    protected $params = [];

    private function __construct()
    {
        
    }

    private function __clone()
    {
        
    }

    /**
     * gets object instance
     * 
     * @return Notification
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * adds element into params container
     * 
     * @param mixed $params
     * @return \Antares\Foundation\Notification
     */
    public function push($params)
    {
        foreach ($params as $key => $value) {
            array_set($this->params, $key, $value);
        }
        return $this;
    }

    /**
     * gets all elements in container
     * 
     * @return array
     */
    public function all()
    {
        return $this->params;
    }

    /**
     * gets variable from params container 
     * 
     * @param String $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return array_get($this->params, $key, $default);
    }

}

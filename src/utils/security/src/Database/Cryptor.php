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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Security\Database;

class Cryptor
{

    /**
     * singleton instance
     *
     * @var Cryptor
     */
    private static $oInstance = false;

    /**
     * config container
     *
     * @var array
     */
    private $config;

    /**
     * singleton get instance
     *
     * @return Cryptor
     */
    public static function getInstance()
    {
        if (self::$oInstance == false) {
            self::$oInstance = new self;
        }
        return self::$oInstance;
    }

    /**
     * constructing
     */
    private function __construct()
    {
        $this->config = config('db_cryptor');
    }

    /**
     * encrypt/decrypt attributes
     * 
     * @param String $action
     * @param String $string
     * @return String
     */
    public function crypt($action, $string)
    {
        if (!array_get($this->config, 'enabled')) {
            return $string;
        }

        $encryptMethod = array_get($this->config, 'config.method');
        $secretKey     = array_get($this->config, 'config.secret_key');
        $secretIv      = array_get($this->config, 'config.secret_iv');
        if (is_null($encryptMethod) or is_null($secretKey) or is_null($secretIv)) {
            return $string;
        }
        $output = false;
        $key    = hash('sha256', $secretKey);
        $iv     = substr(hash('sha256', $secretIv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encryptMethod, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encryptMethod, $key, 0, $iv);
        }

        return $output;
    }

}

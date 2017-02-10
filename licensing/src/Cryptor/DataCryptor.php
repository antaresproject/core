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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Licensing\Cryptor;

use Exception;
use function app;
use function config;

class DataCryptor
{

    /**
     * config container
     *
     * @var array 
     */
    protected $config;

    /**
     * encryption key
     *
     * @var String 
     */
    protected $key;

    /**
     * constructing
     */
    public function __construct()
    {
        $this->config = config('antares/licensing::cryptor');
        $this->key    = app('antares.memory')->make('runtime')->get('instance_key');
    }

    /**
     * decrypts the key
     *
     * @param string $str     The data that contains the key data
     * @param string $keyType The type of the key to encrypt
     * 
     * @return array 
     * */
    public function decrypt($str, $keyType = 'CUSTOM')
    {
        try {
            $randAddOn = substr($str, 0, 3);
            $str       = base64_decode(base64_decode(substr($str, 3)));
            $key       = $this->get_key($keyType);
            if ($this->config['useMcrypt']) {
                $td      = mcrypt_module_open($this->config['algorithm'], '', 'ecb', '');
                $iv      = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
                $key     = substr($key, 0, mcrypt_enc_get_key_size($td));
                mcrypt_generic_init($td, $key, $iv);
                $decrypt = @mdecrypt_generic($td, $str);
                mcrypt_generic_deinit($td);
                mcrypt_module_close($td);
            } else {
                $decrypt = '';
                for ($i = 1; $i <= strlen($str); $i++) {
                    $char    = substr($str, $i - 1, 1);
                    $keychar = substr($key, ($i % strlen($key)) - 1, 1);
                    $char    = chr(ord($char) - ord($keychar));
                    $decrypt .= $char;
                }
            }
            return @unserialize($decrypt);
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * encrypts the key
     *
     * @param array  $srcArray The data array that contains the key data
     * @param string $keyType  The type of the key to encrypt
     * 
     * @return string
     * */
    public function encrypt($srcArray, $keyType = 'CUSTOM')
    {
        $randAddOn = $this->generateRandomString(3);
        $key       = $this->get_key($keyType);

        if ($this->config['useMcrypt']) {
            $td    = mcrypt_module_open($this->config['algorithm'], '', 'ecb', '');
            $iv    = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
            $key   = substr($key, 0, mcrypt_enc_get_key_size($td));
            mcrypt_generic_init($td, $key, $iv);
            $crypt = mcrypt_generic($td, serialize($srcArray));
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
        } else {
            $crypt = '';
            $str   = serialize($srcArray);
            for ($i = 1; $i <= strlen($str); $i++) {
                $char    = substr($str, $i - 1, 1);
                $keychar = substr($key, ($i % strlen($key)) - 1, 1);
                $char    = chr(ord($char) + ord($keychar));
                $crypt .= $char;
            }
        }
        return $randAddOn . base64_encode(base64_encode(trim($crypt)));
    }

    /**
     * gets the hash key for the current encryption
     *
     * @param string $keyType The license key type being produced
     * @return string 
     * */
    protected function get_key($keyType)
    {
        switch ($keyType) {
            case 'KEY' :
                return $this->key;
            case 'CUSTOM' :
                return $this->key;
            case 'REQUESTKEY' :
                return $this->config['hashKey2'];
            case 'KEY1' :
                return $this->config['hashKey1'];
            default :
        }
    }

    /**
     * generates a random string
     *
     * @param number $length The length of the random string
     * @param string $seeds  The string to pluck the characters from
     * 
     * @return string
     * */
    protected function generateRandomString($length = 10, $seeds = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567890123456789')
    {
        $str        = '';
        $seedsCount = strlen($seeds);

        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);

        for ($i = 0; $length > $i; $i++) {
            $str .= $seeds{mt_rand(0, $seedsCount - 1)};
        }
        return $str;
    }

    /**
     * generate random String as license key
     * 
     * @return String
     */
    public function generateKey()
    {
        return $this->generateRandomString(30);
    }

}

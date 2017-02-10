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


namespace Antares\Licensing\Wrapper;

use Antares\Licensing\Cryptor\DataCryptor;

class Wrapper
{

    /**
     * pad string
     *
     * @var String
     */
    protected $pad = "-";

    /**
     * wrap key settings
     *
     * @var number
     */
    protected $wrapto = 80;

    /**
     * license begin string 
     *
     * @var String
     */
    protected $begin1 = 'BEGIN LICENSE KEY';

    /**
     * license end string
     *
     * @var String 
     */
    protected $end1 = 'END LICENSE KEY';

    /**
     * license begin string 
     *
     * @var String
     */
    protected $begin2 = '_DATA{';

    /**
     * license end string
     *
     * @var String 
     */
    protected $end2 = '}DATA_';

    /**
     * DataCryptor instance
     *
     * @var DataCryptor
     */
    protected $cryptor;

    /**
     * constructing
     * 
     * @param DataCryptor $cryptor
     */
    public function __construct(DataCryptor $cryptor)
    {
        $this->cryptor = $cryptor;
    }

    /**
     * wraps up the license key in a nice little package
     *
     * @param array  $srcArray The array that needs to be turned into a license str
     * @param string $keyType  The type of key to be wrapped (KEY=license key, REQUESTKEY=license request key)
     * @return string Returns encrypted and formatted license key
     * */
    public function wrapLicense($srcArray, $keyType = 'KEY')
    {
        // sort the variables
        $begin = $this->pad($this->getBegin($keyType));
        $end   = $this->pad($this->getEnd($keyType));

        // encrypt the data
        $str = $this->cryptor->encrypt($srcArray, $keyType);

        // return the wrap
        return $begin . PHP_EOL . wordwrap($str, $this->wrapto, PHP_EOL, 1) . PHP_EOL . $end;
    }

    /**
     * unwrapLicense
     *
     * unwraps license key back into it's data array
     *
     * @param string $encStr  The encrypted license key string that needs to be decrypted
     * @param string $keyType The type of key to be unwrapped (KEY=license key, REQUESTKEY=license request key)
     * 
     * @return array Returns license data array
     * */
    public function unwrapLicense($encStr, $keyType = 'KEY')
    {
        // sort the variables
        $begin = $this->pad($this->getBegin($keyType));
        $end   = $this->pad($this->getEnd($keyType));

        // get string without seperators
        $str = trim(str_replace(array($begin, $end, "\r", "\n", "\t"), '', $encStr));
        // decrypt and return the key
        return $this->cryptor->decrypt($str);
    }

    /**
     * pad
     *
     * pad out the begin and end seperators
     *
     * @param string $str The string to be padded
     * 
     * @return string Returns the padded string
     * */
    protected function pad($str)
    {
        $strLen = strlen($str);
        $spaces = ($this->wrapto - $strLen) / 2;
        $str1   = '';
        for ($i = 0; $i < $spaces; $i++) {
            $str1 = $str1 . $this->pad;
        }
        if ($spaces / 2 != round($spaces / 2)) {
            $str = substr($str1, 0, strlen($str1) - 1) . $str;
        } else {
            $str = $str1 . $str;
        }
        $str = $str . $str1;
        return $str;
    }

    /**
     * getBegin
     *
     * gets the begining license key seperator text
     *
     * @param string $keyType string The license key type being produced
     * 
     * @return string Returns the begining string
     * */
    protected function getBegin($keyType)
    {
        switch ($keyType) {
            case 'KEY' :
                return $this->begin1;
            case 'REQUESTKEY' :
                return $this->begin2;
            case 'HOMEKEY' :
                return '';
        }
    }

    /**
     * getEnd
     *
     * gets the ending license key seperator text
     *
     * @param string $keyType The license key type being produced
     * 
     * @return string Returns the ending string
     * */
    protected function getEnd($keyType)
    {
        switch ($keyType) {
            case 'KEY' :
                return $this->end1;
            case 'REQUESTKEY' :
                return $this->end2;
            case 'HOMEKEY' :
                return '';
        }
    }

}

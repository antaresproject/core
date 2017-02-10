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


namespace Antares\Licensing\Server;

class System
{

    /**
     * this is the number of required server stats for the key generation to be successfull
     * if the server can't produce this number of details then the key fails to be generated
     * you can set it to however many you wish, the max is 5
     *
     * @var number
     */
    protected $requiredUris = 2;

    /**
     * server info container
     *
     * @var array
     */
    public $serverInfo = array();

    /**
     * server variables 
     *
     * @var array
     */
    public $serverVars = array();

    /**
     * ips container
     *
     * @var array 
     */
    public $ips = array();

    /**
     * mac address
     *
     * @var String
     */
    public $mac;

    /**
     * setServerVars
     *
     * to protect against spoofing you should copy the $server vars into a
     * separate array right at the first line of your script so parameters can't
     * be changed in unencoded php files. This doesn't have to be set. If it is
     * not set then the $server is copied when _getServerInfo (private) function
     * is called.
     *
     * @param array $array The copied $server array
     *
     * @return void 
     * */
    public function setServerVars($server)
    {
        $this->serverVars = $server;
        // some of the ip data is dependant on the $server vars, so update them
        // after the vars have been set
        $this->ips        = $this->getIpAddress();
        // update the server info
        $this->serverInfo = $this->getServerInfo();

        $this->mac = $this->getMacAddress();
    }

    /**
     * getIpAddress
     *
     * Used to get the MAC address of the host server. It works with Linux,
     * Darwin (Mac OS X), and Win XP. It may work with others as some other
     * os's have similar ifconfigs to Darwin but they haven't been tested
     *
     * @return array IP Address(s) if found (Note one machine may have more than one ip)
     * @return string ERROR_OPEN means config can't be found and thus not opened
     * @return string IP_404 means ip adress doesn't exist in the config file and can't be found in the $server
     * @return string SAFE_MODE means server is in safe mode so config can't be read
     * */
    public function getIpAddress()
    {
        $ips  = array();
        // get the cofig file
        $conf = $this->getConfig();
        // if the conf has returned and error return it
        if ($conf != 'SAFE_MODE' && $conf != 'ERROR_OPEN') {
            // if anyone has any clues for windows environments
            // or other server types let me know
            $os = strtolower(PHP_OS);
            if (substr($os, 0, 3) == 'win') {
                // anyone any clues on win ip's
            } else {

                // explode the conf into seperate lines for searching
                $lines   = explode(PHP_EOL, $conf);
                // get the ip delim
                $ipDelim = $this->getOsVar('ip', $os);

                // ip pregmatch
                $num = "(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])";
                // seperate the lines
                foreach ($lines as $key => $line) {
                    // check for the ip signature in the line
                    if (!preg_match("/^$num\\.$num\\.$num\\.$num$/", $line) && strpos($line, $ipDelim)) {
                        // seperate out the ip
                        $ip = substr($line, strpos($line, $ipDelim) + strlen($ipDelim));
                        $ip = trim(substr($ip, 0, strpos($ip, " ")));
                        // add the ip to the collection
                        if (!isset($ips[$ip])) {
                            $ips[$ip] = $ip;
                        }
                    }
                }
            }
        }
        // if the conf has returned nothing
        // attempt to use the $server data
        if (isset($this->serverVars['SERVER_NAME'])) {
            $ip = gethostbyname($this->serverVars['SERVER_NAME']);
            if (!isset($ips[$ip])) {
                $ips[$ip] = $ip;
            }
        }
        if (php_sapi_name() == "cli") {
            $localIP       = getHostByName(getHostName());
            $ips[$localIP] = $localIP;
        } elseif (isset($this->serverVars['SERVER_ADDR'])) {

            $name = gethostbyaddr($this->serverVars['SERVER_ADDR']);
            $ip   = gethostbyname($name);
            if (!isset($ips[$ip])) {
                $ips[$ip] = $ip;
            }
            // if the $server addr is not the same as the returned ip include it aswell
            if (isset($addr) && $addr != $this->serverVars['SERVER_ADDR']) {
                if (!isset($ips[$this->serverVars['SERVER_ADDR']])) {
                    $ips[$this->serverVars['SERVER_ADDR']] = $this->serverVars['SERVER_ADDR'];
                }
            }
        }

        // count return ips and return if found
        if (count($ips) > 0) {
            return $ips;
        }
        // failed to find an ip check for conf error or return 404
        if ($conf == 'SAFE_MODE' || $conf == 'ERROR_OPEN') {
            return $conf;
        }
        return 'IP_404';
    }

    /**
     * getMacAddress
     *
     * Used to get the MAC address of the host server. It works with Linux,
     * Darwin (Mac OS X), and Win XP. It may work with others as some other
     * os's have similar ifconfigs to Darwin but they haven't been tested
     *
     * @access private
     * @return string Mac address if found
     * @return string ERROR_OPEN means config can't be found and thus not opened
     * @return string MAC_404 means mac adress doesn't exist in the config file
     * @return string SAFE_MODE means server is in safe mode so config can't be read
     * */
    public function getMacAddress()
    {
        // open the config file
        $conf = $this->getConfig();

        // if anyone has any clues for windows environments
        // or other server types let me know
        $os = strtolower(PHP_OS);
        if (substr($os, 0, 3) == 'win') {
            // explode the conf into lines to search for the mac
            $lines = explode(PHP_EOL, $conf);
            // seperate the lines for analysis
            foreach ($lines as $key => $line) {
                // check for the mac signature in the line
                // originally the check was checking for the existence of string 'physical address'
                // however Gert-Rainer Bitterlich pointed out this was for english language
                // based servers only. preg_match updated by Gert-Rainer Bitterlich. Thanks
                if (preg_match("/([0-9a-f][0-9a-f][-:]){5}([0-9a-f][0-9a-f])/i", $line)) {
                    $trimmedLine = trim($line);
                    // take of the mac addres and return
                    return trim(substr($trimmedLine, strrpos($trimmedLine, " ")));
                }
            }
        } else {
            // get the mac delim
            $macDelim = $this->getOsVar('mac', $os);

            // get the pos of the os_var to look for
            $pos = strpos($conf, $macDelim);
            if ($pos) {
                // seperate out the mac address
                $str1 = trim(substr($conf, ($pos + strlen($macDelim))));
                return trim(substr($str1, 0, strpos($str1, "\n")));
            }
        }
        // failed to find the mac address
        return 'MAC_404';
    }

    /**
     * getServerInfo
     *
     * used to generate the server binds when server binding is needed.
     *
     * @return array server bindings
     * @return boolean false means that the number of bindings failed to
     *      meet the required number
     * */
    public function getServerInfo()
    {
        $server = $_SERVER;
        if (empty($this->serverVars)) {
            $this->setServerVars($server);
        }
        $a = [
            'SERVER_ADDR'     => getHostByName(getHostName()),
            'SCRIPT_FILENAME' => getcwd() . ((php_sapi_name() != "cli") ? '' : DIRECTORY_SEPARATOR . 'public')
        ];
        if (php_sapi_name() != "cli" and count($a) < $this->requiredUris) {
            return 'SERVER_FAILED';
        }
        return $a;
    }

    /**
     * getOsVar
     *
     * gets various vars depending on the os type
     *
     * @param type $varName The var name
     * @param type $os      The os name
     * 
     * @return string various values
     * */
    protected function getOsVar($varName, $os)
    {
        $varName = strtolower($varName);
        // switch between the os's
        switch ($os) {
            // not sure if the string is correct for FreeBSD
            // not tested
            case 'freebsd' :
            // not sure if the string is correct for NetBSD
            // not tested
            case 'netbsd' :
            // not sure if the string is correct for Solaris
            // not tested
            case 'solaris' :
            // not sure if the string is correct for SunOS
            // not tested
            case 'sunos' :
            // darwin is mac os x
            // tested only on the client os
            case 'darwin' :
                // switch the var name
                switch ($varName) {
                    case 'conf' :
                        $var = '/sbin/ifconfig';
                        break;
                    case 'mac' :
                        $var = 'ether';
                        break;
                    case 'ip' :
                        $var = 'inet ';
                        break;
                }
                break;
            // linux variation
            // tested on server
            case 'linux' :
                // switch the var name
                switch ($varName) {
                    case 'conf' :
                        $var = '/sbin/ifconfig';
                        break;
                    case 'mac' :
                        $var = 'HWaddr';
                        break;
                    case 'ip' :
                        $var = 'inet addr:';
                        break;
                }
                break;
        }
        return $var;
    }

    /**
     * getConfig
     *
     * gets the server config file and returns it. tested on Linux,
     * Darwin (Mac OS X), and Win XP. It may work with others as some other
     * os's have similar ifconfigs to Darwin but they haven't been tested
     *
     * @return string config file data
     * */
    public function getConfig()
    {
        if (ini_get('safe_mode')) {
            // returns invalid because server is in safe mode thus not allowing
            // sbin reads but will still allow it to open. a bit weird that one.
            return 'SAFE_MODE';
        }
        // if anyone has any clues for windows environments
        // or other server types let me know
        $os = strtolower(PHP_OS);
        if (substr($os, 0, 3) == 'win') {
            // this windows version works on xp running apache
            // based server. it has not been tested with anything
            // else, however it should work with NT, and 2000 also
            // execute the ipconfig
            @exec('ipconfig/all', $lines);
            // count number of lines, if none returned return MAC_404
            // thanks go to Gert-Rainer Bitterlich <bitterlich -at- ima-dresden -dot- de>
            if (count($lines) == 0) {
                return 'ERROR_OPEN';
            }
            // $path the lines together
            $conf = implode(PHP_EOL, $lines);
        } else {
            // get the conf file name
            $osFile = $this->getOsVar('conf', $os);
            // open the ipconfig
            $fp     = @popen($osFile, "rb");
            // returns invalid, cannot open ifconfig
            if (!$fp) {
                return 'ERROR_OPEN';
            }
            // read the config
            $conf = @fread($fp, 4096);
            @pclose($fp);
        }
        return $conf;
    }

}

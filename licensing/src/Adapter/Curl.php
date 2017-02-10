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


namespace Antares\Licensing\Adapter;

use Antares\Licensing\Cryptor\DataCryptor;
use Antares\Support\Facades\Foundation;
use Antares\Licensing\Server\System;

class Curl
{

    /**
     * config container
     *
     * @var type 
     */
    protected $config;

    /**
     * DataCryptor instance
     *
     * @var DataCryptor 
     */
    protected $cryptor;

    /**
     * System instance
     *
     * @var System 
     */
    protected $system;

    /**
     * constructing
     * 
     * @param DataCryptor $cryptor
     * @param System $system
     */
    public function __construct(DataCryptor $cryptor, System $system)
    {
        $this->config  = config('antares/licensing::validation');
        $this->cryptor = $cryptor;
        $this->system  = $system;
    }

    /**
     * realize curl connetion to validate license data
     * 
     * @param String $query
     * @return array
     */
    protected function curl($query)
    {
        $host = array_get($this->config, 'host');
        if (!$this->ping($host)) {
            return false;
        }
        $activeExtensions = antares('memory')->get('extensions.active', []);
        $key              = app('antares.memory')->make('runtime')->get('instance_key');
        $fields           = [
            'POSTDATA'      => $this->cryptor->encrypt($query, 'CUSTOM', $key),
            'CLIENTS_COUNT' => Foundation::make('antares.user')->clients()->count(),
            'EXTENSIONS'    => serialize(array_keys($activeExtensions)),
            'MAC'           => $this->system->getMacAddress(),
            'SERVER'        => serialize($this->system->getServerInfo()),
            'IP'            => serialize($this->system->getIpAddress()),
            'ID'            => $key
        ];
        $appKey           = config('app.key');
        $ch               = curl_init();

        curl_setopt($ch, CURLOPT_URL, $host . array_get($this->config, 'path'));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["license:  {$appKey}"]);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    /**
     * ping to licensing server
     * 
     * @param String $domain
     * @return boolean
     */
    protected function ping($domain)
    {
        $domain = str_replace([ ':', '/', 'http', 'https'], '', $domain);
        try {
            $file = fsockopen($domain, 80, $errno, $errstr, 1);
            fclose($file);
            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * callHome
     *
     * calls the dial home server (your server) andvalidates the clients license
     * with the info in the mysql db
     *
     * @param array $query
     * @return string Returns: the encrypted server validation result from the dial home call
     *                       : SOCKET_FAILED    => socket failed to connect to the server
     * */
    protected function callHome(array $query)
    {
        $data = $this->curl($query);
        return (empty($data['RESULT'])) ? 'SOCKET_FAILED' : $data['RESULT'];
    }

    /**
     * sends license details to license server
     * 
     * @param array $data
     * @return array
     */
    public function send(array $data)
    {
        return $this->callHome($data);
    }

}

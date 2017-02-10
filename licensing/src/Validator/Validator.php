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


namespace Antares\Licensing\Validator;

use Antares\Licensing\Wrapper\Wrapper;
use Illuminate\Filesystem\Filesystem;
use Antares\Licensing\Server\System;
use Antares\Licensing\Adapter\Curl;
use Exception;

class Validator
{

    /**
     * time checking start period difference allowance ie if the user has slightly different time
     * setting on their server make an allowance for the diff period. carefull to not make it too
     * much otherwise they could just reset their server to a time period before the license expires.
     *
     * @var number (seconds)
     */
    protected $startDif = 129600;

    /**
     * System instance
     *
     * @var System 
     */
    protected $system;

    /**
     * Filesystem
     *
     * @var Filesystem instance
     */
    protected $filesystem;

    /**
     * Curl adapter instance
     *
     * @var Curl 
     */
    protected $curlAdapter;

    /**
     * Wrapper
     *
     * @var Wrapper instance 
     */
    protected $wrapper;

    /**
     * data container
     *
     * @var array 
     */
    protected $data;

    /**
     * the date string for human readable format
     *
     * @var string
     */
    protected $dateString = 'd.m.Y H:i:s';

    /**
     * mac address
     *
     * @var String 
     */
    protected $mac;

    /**
     * server vars container
     *
     * @var array 
     */
    protected $serverVars = [];

    /**
     * ips container
     *
     * @var array
     */
    protected $ips = [];

    /**
     * The number of allowed differences between the $SERVER vars and the vars
     * stored in the key
     *
     * @var number
     */
    protected $allowedServerDifs = 0;

    /**
     * The number of allowed differences between the $ip vars in the key and the ip
     * vars collected from the server
     *
     * @var number
     */
    protected $allowedIpDifs = 1;

    /**
     * constructing
     * 
     * @param System $system
     * @param Filesystem $filesystem
     * @param Curl $curlAdapter
     * @param Wrapper $wrapper
     */
    public function __construct(System $system, Filesystem $filesystem, Curl $curlAdapter, Wrapper $wrapper)
    {
        $this->system      = $system;
        $this->filesystem  = $filesystem;
        $this->curlAdapter = $curlAdapter;
        $this->wrapper     = $wrapper;
    }

    /**
     * runs license validation
     * 
     * @return array
     * @throws Exception
     */
    public function validate()
    {
        $path       = storage_path('license');
        $cert       = $this->filesystem->glob($path . '/*.cert');
        $key        = $this->filesystem->glob($path . '/*.key');
        $redirectTo = config('antares/licensing::redirect_route');
        //$response   = new RedirectResponse(handles($redirectTo));
        $requestUri = app('request')->getRequestUri();
        if ($requestUri == '/') {
            return;
        }
        $requestMatch = app('request')->getRequestUri() == '/' . $redirectTo;
        if (empty($key)) {
            if ($requestMatch) {
                return ['RESULT' => 'LICENSE_KEY_FILE_NOT_EXISTS'];
            } else {
                header('Location: ' . handles($redirectTo));
                exit();
            }
        }
        $key = current($key);
        if (empty($cert)) {
            if ($requestMatch) {
                return ['RESULT' => 'LICENSE_FILE_NOT_EXISTS'];
            } else {
                header('Location: ' . handles($redirectTo));
                exit();
            }
        }

        $cert = current($cert);
        $this->system->setServerVars($_SERVER);
        return $this->doValidate($this->filesystem->get($cert));
    }

    /**
     * validate license
     * 
     * @param String $license
     * @return array
     */
    protected function doValidate($license)
    {
        if (!strlen($license) > 0) {
            return ['RESULT' => 'LICENSE_IS_EMPTY'];
        }
        $data = $this->wrapper->unwrapLicense($license);
        if (!is_array($data)) {
            return ['RESULT' => 'LICENSE_DATA_INVALID'];
        }
        if ($data['DATE']['START'] > time() + $this->startDif) {
            $data['RESULT'] = 'TMINUS';
        }
        if ($data['DATE']['END'] - time() < 0 && $data['DATE']['SPAN'] != 'NEVER') {
            $data['RESULT'] = 'EXPIRED';
        }
        $data['DATE']['HUMAN']['START'] = date($this->dateString, (int) last(explode('.', $data['DATE']['START'])));
        $data['DATE']['HUMAN']['END']   = date($this->dateString, (int) last(explode('.', $data['DATE']['END'])));
        $domain                         = $this->compareDomainIp($data['SERVER']['DOMAIN'], $this->system->ips);
        if (!$domain) {
            $data['RESULT'] = 'ILLEGAL';
        }
        if (!isset($data['RESULT'])) {
            $data['RESULT'] = 'OK';
        }
        if (app('antares.installed')) {
            $data['RESULT'] = $this->curlAdapter->send(['LICENSE_DATA' => $data + ['KEY' => md5($license)]]);
        }

        return $data;
    }

    /**
     * uses the supplied domain in the key and runs a check against the collected
     * ip addresses. If there are matching ips it returns true as the domain
     * and ip address match up
     *
     * @param type  $domain The domain to compare
     * @param mixed $ips    The IPs array or false
     * @return boolean
     * */
    public function compareDomainIp($domain, $ips = false)
    {
        if (!$ips) {
            $ips = $this->system->getIpAddress();
        }
        $domainIps = gethostbynamel($domain);
        if (is_array($domainIps) && count($domainIps) > 0) {
            foreach ($domainIps as $ip) {
                if (in_array($ip, $ips)) {
                    return true;
                }
            }
        }
        return false;
    }

}

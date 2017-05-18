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

namespace Antares\GeoIP;

use Illuminate\Session\Store as SessionStore;
use Torann\GeoIP\GeoIP as SupportGeoIP;
use Antares\Config\Repository;
use Guzzle\Http\Client;
use Exception;

class GeoIP extends SupportGeoIP
{

    /**
     * External IP checker url
     */
    const LOCATION = 'http://freegeoip.net/json/';

    /**
     * External IP checker url
     */
    const SERVICE = 'http://ipecho.net/plain';

    /**
     * Create a new GeoIP instance.
     *
     * @param  \Illuminate\Config\Repository  $config
     * @param  \Illuminate\Session\Store      $session
     */
    public function __construct(Repository $config, SessionStore $session)
    {
        $this->config                 = $config;
        $this->session                = $session;
        $this->default_location       = array_merge(
                $this->default_location, $this->config->get('geoip.default_location', array())
        );
        $this->remote_ip              = $this->default_location['ip'] = $this->getClientIP();
    }

    public function getLocation($ip = null)
    {
        try {
            $ip       = is_null($ip) ? request()->ip() : $ip;
            $client   = new Client();
            $res      = $client->createRequest('GET', self::LOCATION . $ip);
            $response = $res->send();
            if ($response->getStatusCode() === 200) {
                $body = $response->getBody(true);
                return json_decode($body, true);
            }
        } catch (Exception $ex) {
            
        }
    }

    /**
     * Get the client IP address.
     *
     * @return string
     */
    public function getClientIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = '127.0.0.0';
        }
        return $ipaddress;
    }

}

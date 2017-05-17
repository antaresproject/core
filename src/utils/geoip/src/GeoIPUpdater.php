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

use GuzzleHttp\Client as Client;
use Antares\Config\Repository;
use Torann\GeoIP\GeoIPUpdater as TorannGeoIPUpdater;

class GeoIPUpdater extends TorannGeoIPUpdater
{

    /**
     * @param array $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

}

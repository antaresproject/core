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

use Torann\GeoIP\GeoIPServiceProvider as SupportGeoIpServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use Antares\GeoIP\Console\UpdateCommand;

class GeoIPServiceProvider extends SupportGeoIpServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Register providers.
        $this->app->singleton('geoip', function (Application $app) {
            return new GeoIP($app['config'], $app["session.store"]);
        });
        $this->app->singleton('command.geoip.update', function (Application $app) {
            return new UpdateCommand($app['config']);
        });
        $this->commands(['command.geoip.update']);
    }

}

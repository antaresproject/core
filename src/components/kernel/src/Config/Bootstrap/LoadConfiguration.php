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


namespace Antares\Config\Bootstrap;

use Illuminate\Support\Arr;
use Antares\Config\FileLoader;
use Antares\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;

class LoadConfiguration
{

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     *
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $env    = null;
        $items  = [];
        $loader = new FileLoader(new Filesystem(), $app->configPath());

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if (file_exists($cached = $app->getCachedConfigPath())) {
            $items = require $cached;

            $env = Arr::get($items, '*::app.env');
        }

        $this->setEnvironment($app, $env);

        $app->instance('config', $config = (new Repository($loader, $app->environment())));

        $config->setFromCache($items);


        date_default_timezone_set($config['app.timezone']);

        mb_internal_encoding('UTF-8');
    }

    /**
     * Set application environment.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  string|null  $env
     *
     * @return void
     */
    protected function setEnvironment(Application $app, $env = null)
    {
        $app->detectEnvironment(function () use ($env) {
            return $env ? : env('APP_ENV', 'production');
        });
    }

}

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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Bootstrap;

use Antares\Customfields\Memory\FormsProvider;
use Antares\Customfields\Memory\FormsRepository;
use Illuminate\Contracts\Foundation\Application;

class LoadMemoryData
{

    /**
     * Bootstrap the given application.
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        if (!app('antares.extension')->isActive('customfields')) {
            return false;
        }
        $app->make('antares.memory')->extend('registry', function ($app, $name) {
            $handler = new FormsRepository($name, [], $app);
            return new FormsProvider($handler);
        });
    }

}

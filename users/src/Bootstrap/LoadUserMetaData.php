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


namespace Antares\Users\Bootstrap;

use Antares\Model\Memory\UserMetaProvider;
use Antares\Model\Memory\UserMetaRepository;
use Illuminate\Contracts\Foundation\Application;

class LoadUserMetaData
{

    /**
     * Bootstrap the given application.
     * @param  \Illuminate\Contracts\Users\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $app->make('antares.memory')->extend('user', function ($app, $name) {
            $handler = new UserMetaRepository($name, [], $app);
            return new UserMetaProvider($handler);
        });
    }

}

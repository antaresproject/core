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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Extension\Traits;

use Antares\Extension\RouteGenerator;

trait DomainAwareTrait
{

    /**
     * Register domain awareness from configuration.
     *
     * @return void
     */
    public function registerDomainAwareness()
    {
        $this->app->afterResolving(function (RouteGenerator $generator, $app) {
            $generator->setBaseUrl($app->make('config')->get('app.url'));
        });
    }

}

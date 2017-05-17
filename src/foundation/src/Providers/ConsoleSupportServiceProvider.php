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

namespace Antares\Foundation\Providers;

use Illuminate\Support\AggregateServiceProvider;

class ConsoleSupportServiceProvider extends AggregateServiceProvider
{

    /**
     * The provider class names.
     *
     * @var array
     */
    protected $providers = [
        'Antares\Foundation\Providers\ArtisanServiceProvider',
        //'Illuminate\Console\ScheduleServiceProvider',
        'Antares\Database\MigrationServiceProvider',
        //'Illuminate\Database\SeedServiceProvider',
        'Illuminate\Foundation\Providers\ComposerServiceProvider',
        //'Illuminate\Queue\ConsoleServiceProvider',
        'Antares\Auth\CommandServiceProvider',
        'Antares\Extension\CommandServiceProvider',
        'Antares\Memory\CommandServiceProvider',
        'Antares\Foundation\Providers\CommandServiceProvider',
        'Antares\Publisher\CommandServiceProvider',
        'Antares\View\CommandServiceProvider',
    ];

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

}

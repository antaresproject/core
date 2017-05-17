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
 namespace Antares\Memory;

use Antares\Memory\Console\MemoryCommand;
use Antares\Support\Providers\CommandServiceProvider as ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Memory' => 'antares.commands.memory',
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function registerMemoryCommand()
    {
        $this->app->singleton('antares.commands.memory', function () {
            return new MemoryCommand();
        });
    }
}

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

use Antares\Foundation\Console\Commands\CoreAclCommand;
use Illuminate\Contracts\Foundation\Application;
use Antares\Foundation\Console\Commands\QueueCommand;
use Antares\Foundation\Console\Commands\AssembleCommand;
use Antares\Support\Providers\CommandServiceProvider as ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Assemble'  => 'antares.commands.assemble',
        'Queue'     => 'antares.commands.queue',
        'CoreAcl'   => 'antares.commands.core-acl',
    ];

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerAssembleCommand()
    {
        $this->app->singleton('antares.commands.assemble', function (Application $app) {
            $foundation = $app->make('antares.app');

            return new AssembleCommand($foundation, $foundation->memory());
        });
    }

    /**
     * Register the queue command
     *
     * @return void
     */
    protected function registerQueueCommand()
    {
        $this->app->singleton('antares.commands.queue', function () {
            return new QueueCommand();
        });
    }

    /**
     * Register the queue command
     *
     * @return void
     */
    protected function registerCoreAclCommand()
    {
        $this->app->singleton('antares.commands.core-acl', function () {
            return new CoreAclCommand();
        });
    }

}

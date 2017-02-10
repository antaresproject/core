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


namespace Antares\Licensing;

use Antares\Support\Providers\CommandServiceProvider as ServiceProvider;
use Antares\Licensing\Console\GenerateCommand;

class CommandServiceProvider extends ServiceProvider
{

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        'Generator' => 'antares.commands.license.generate',
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    protected function registerGeneratorCommand()
    {
        $this->app->singleton('antares.commands.license.generate', function () {
            return new GenerateCommand();
        });
    }

}

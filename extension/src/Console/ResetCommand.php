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
 namespace Antares\Extension\Console;

use Illuminate\Console\ConfirmableTrait;

class ResetCommand extends ExtensionCommand
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset an extension.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $name = $this->argument('name');

        $this->laravel['antares.extension.finder']->detect();

        $reset = $this->laravel['antares.extension']->reset($name);

        if (!! $reset) {
            $this->info("Extension [{$name}] has been reset.");
        } else {
            $this->error("Unable to reset extension [{$name}].");
        }
    }
}

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

class RefreshCommand extends ExtensionCommand
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh an extension.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $name = $this->argument('name');

        $refresh = $this->laravel['antares.extension']->refresh($name);

        if (!! $refresh) {
            $this->info("Extension [{$name}] refreshed.");
        } else {
            $this->error("Unable to refresh extension [{$name}].");
        }
    }
}

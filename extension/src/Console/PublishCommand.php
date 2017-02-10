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

use Illuminate\Support\Fluent;
use Illuminate\Console\ConfirmableTrait;
use Antares\Extension\Processor\Migrator as Processor;
use Antares\Contracts\Extension\Listener\Migrator as Listener;

class PublishCommand extends ExtensionCommand implements Listener
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migration and asset publishing for an extension.';

    /**
     * Execute the console command.
     *
     * @param  \Antares\Extension\Processor\Migrator  $migrator
     *
     * @return void
     */
    public function handle(Processor $migrator)
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        return $migrator->migrate($this, new Fluent(['name' => $this->argument('name')]));
    }

    /**
     * Response when extension migration has failed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     * @param  array  $errors
     *
     * @return mixed
     */
    public function migrationHasFailed(Fluent $extension, array $errors)
    {
        $this->error("Extension [{$extension->get('name')}] update has failed.");
    }

    /**
     * Response when extension migration has succeed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function migrationHasSucceed(Fluent $extension)
    {
        $this->info("Extension [{$extension->get('name')}] updated.");
    }
}

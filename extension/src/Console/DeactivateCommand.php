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
use Antares\Extension\Processor\Deactivator as Processor;
use Antares\Contracts\Extension\Listener\Deactivator as Listener;

class DeactivateCommand extends ExtensionCommand implements Listener
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:deactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate an extension.';

    /**
     * Execute the console command.
     *
     * @param  \Antares\Extension\Processor\Deactivator  $deactivator
     *
     * @return void
     */
    public function handle(Processor $deactivator)
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        return $deactivator->deactivate($this, new Fluent(['name' => $this->argument('name')]));
    }

    /**
     * Response when extension deactivation has succeed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function deactivationHasSucceed(Fluent $extension)
    {
        $this->info("Extension [{$extension->get('name')}] deactivated.");
    }
}

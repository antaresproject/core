<?php

declare(strict_types = 1);

namespace Antares\Extension\Console;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Processors\Activator;
use Illuminate\Console\ConfirmableTrait;

class ActiveCommand extends ExtensionCommand
{

    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Active an extension.';

    /**
     * Execute the console command.
     *
     * @param Activator $activator
     * @throws \Exception
     */
    public function handle(Activator $activator)
    {
//		if (! $this->confirm('Do you wish to continue? [y|N]')) {
//			return;
//		}

        $name      = $this->argument('name');
        $extension = $this->manager->getAvailableExtensions()->findByName($name);

        if ($extension instanceof ExtensionContract) {
            $activator->run($this, $extension);
        } else {
            $this->error("Unable to find extension [{$name}].");
        }
    }

}

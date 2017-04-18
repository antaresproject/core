<?php

declare(strict_types=1);

namespace Antares\Extension\Console;

use Antares\Extension\Contracts\ExtensionContract;
use Illuminate\Console\ConfirmableTrait;
use Antares\Extension\Processors\Uninstaller;

class UninstallCommand extends ExtensionCommand {

	use ConfirmableTrait;


	/**
	 * The console command signature.
	 *
	 * @var string
	 */
    protected $signature = 'extension:uninstall {name} {--purge}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Uninstall an extension.';

    /**
     * @var array
     */
    protected $validOptions = [
        'purge',
    ];

	/**
	 * Execute the console command.
	 *
	 * @param Uninstaller $uninstaller
     * @throws \Exception
	 */
	public function handle(Uninstaller $uninstaller)
	{
		if (! $this->confirm('Do you wish to continue? [y|N]')) {
			return;
		}

		$name	    = $this->argument('name');
        $extension  = $this->manager->getAvailableExtensions()->findByName($name);

        if($extension instanceof ExtensionContract) {
            $uninstaller->run($this, $extension, $this->getValidOptions());
        }
        else {
            $this->error("Unable to find extension [{$name}].");
        }
	}

}

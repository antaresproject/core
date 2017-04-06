<?php

declare(strict_types=1);

namespace Antares\Extension\Console;

use Antares\Extension\Contracts\ExtensionContract;
use Illuminate\Console\ConfirmableTrait;
use Antares\Extension\Processors\Installer;

class InstallCommand extends ExtensionCommand {

	use ConfirmableTrait;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'extension:install';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install an extension.';

	/**
	 * Execute the console command.
	 *
	 * @param Installer $installer
     * @throws \Exception
	 */
	public function handle(Installer $installer) {
		if (! $this->confirm('Do you wish to continue? [y|N]')) {
			return;
		}

		$name	    = $this->argument('name');
		$extension  = $this->manager->getAvailableExtensions()->findByName($name);

		if($extension instanceof ExtensionContract) {
			$installer->run($this, $extension);
		}
		else {
			$this->error("Unable to find extension [{$name}].");
		}
	}

}

<?php

declare(strict_types=1);

namespace Antares\Extension\Console;

use Antares\Extension\Contracts\ExtensionContract;
use Illuminate\Console\ConfirmableTrait;
use Antares\Extension\Processors\Installer;

class InstallCommand extends ExtensionCommand {

	use ConfirmableTrait;

	/**
	 * The console command signature.
	 *
	 * @var string
	 */
	protected $signature = 'extension:install {name} {--skip-composer}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Install an extension.';

    /**
     * @var array
     */
	protected $validOptions = [
	    'skip-composer',
    ];

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
			$installer->run($this, $extension, $this->getValidOptions());
		}
		else {
			$this->error("Unable to find extension [{$name}].");
		}
	}

}

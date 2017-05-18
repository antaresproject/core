<?php

declare(strict_types=1);

namespace Antares\Extension\Console;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Processors\Deactivator;
use Illuminate\Console\ConfirmableTrait;

class DeactiveCommand extends ExtensionCommand {

	use ConfirmableTrait;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'extension:deactive';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Deactive an extension.';

	/**
	 * Execute the console command.
	 *
	 * @param Deactivator $deactivator
     * @throws \Exception
	 */
	public function handle(Deactivator $deactivator) {
		if (! $this->confirm('Do you wish to continue? [y|N]')) {
			return;
		}

		$name	    = $this->argument('name');
		$extension  = $this->manager->getAvailableExtensions()->findByName($name);

		if($extension instanceof ExtensionContract) {
            $deactivator->run($this, $extension);
		}
		else {
			$this->error("Unable to find extension [{$name}].");
		}
	}

}

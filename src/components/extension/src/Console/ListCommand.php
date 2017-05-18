<?php

declare(strict_types=1);

namespace Antares\Extension\Console;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Exception\ExtensionException;
use Illuminate\Console\Command;
use Antares\Extension\Manager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class ListCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'extension:list';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'List extensions.';

    /**
     * @var array
     */
	protected static $columns = ['name', 'version', 'required', 'installed', 'activated', 'path', 'namespace'];

	/**
	 * Execute the console command.
	 *
	 * @param Manager $manager
     * @throws ExtensionException
     * @throws FileNotFoundException
	 */
	public function handle(Manager $manager) {
		$extensions = $manager->getAvailableExtensions()->map(function(ExtensionContract $extension) {
			$mapped = array_intersect_key($extension->toArray(), array_flip(static::$columns));

            $mapped['required']  = $mapped['required'] ? 'Yes' : 'No';
			$mapped['installed'] = $mapped['installed'] ? 'Yes' : 'No';
            $mapped['activated'] = $mapped['activated'] ? 'Yes' : 'No';

            return $mapped;
		});

        $this->table(static::$columns, $extensions->toArray());
	}

}

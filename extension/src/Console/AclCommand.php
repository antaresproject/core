<?php

declare(strict_types=1);

namespace Antares\Extension\Console;

use Antares\Acl\Migration;
use Antares\Acl\RoleActionList;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Exception\ExtensionException;
use Illuminate\Console\Command;
use Antares\Extension\Manager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use File;
use Log;

class AclCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'extension:acl';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Refresh extensions ACL.';

    /**
     * ACL migration instance.
     *
     * @var Migration
     */
	protected $migration;

    /**
     * AclCommand constructor.
     * @param Migration $migration
     */
    public function __construct(Migration $migration) {
        parent::__construct();

        $this->migration = $migration;
    }

    /**
	 * Execute the console command.
	 *
	 * @param Manager $manager
     * @throws ExtensionException
     * @throws FileNotFoundException
	 */
	public function handle(Manager $manager) {
        $extensions = $manager->getAvailableExtensions()->filterByActivated();

        foreach($extensions as $extension) {
            $this->importAcl($extension);
        }
	}

    /**
     * @param ExtensionContract $extension
     */
	private function importAcl(ExtensionContract $extension) {
        $name = $extension->getPackage()->getName();

	    try {
            $roleActionList = File::getRequire($extension->getPath() . '/acl.php');

            if($roleActionList instanceof RoleActionList) {
                $this->migration->down($name);
                $this->migration->up($name, $roleActionList);

                $this->info('Migrating for ' . $name);
            }
        }
        catch(FileNotFoundException $e) {
            $this->info('Skipped for ' . $name);
            // No need to throw an exception because of ACL file can be optional. In that case the required file will be not found.
        }
        catch(\Exception $e) {
	        Log::error($e->getMessage(), $e->getTrace());
            $this->error($e->getMessage());
        }
    }

}

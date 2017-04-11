<?php

declare(strict_types=1);

namespace Antares\Extension\Console;

use Antares\Acl\Migration;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Model\Operation;
use Antares\Extension\Processors\Acl;
use Illuminate\Console\Command;
use Antares\Extension\Manager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class AclCommand extends Command implements OperationHandlerContract {

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
     * ACL Processor.
     *
     * @var Acl
     */
	protected $acl;

    /**
     * AclCommand constructor.
     * @param Migration $migration
     */
    public function __construct(Acl $acl) {
        parent::__construct();

        $this->acl = $acl;
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
            $this->acl->import($this, $extension);
        }
	}

    /**
     * @param Operation $operation
     * @return mixed
     */
    public function operationSuccess(Operation $operation)
    {
        $this->info($operation->getMessage());
    }

    /**
     * @param Operation $operation
     * @return mixed
     */
    public function operationFailed(Operation $operation)
    {
        $this->error($operation->getMessage());
    }

    /**
     * @param Operation $operation
     * @return mixed
     */
    public function operationInfo(Operation $operation)
    {
        $this->info($operation->getMessage());
    }

}

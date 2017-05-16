<?php

declare(strict_types = 1);

namespace Antares\Extension\Console;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Model\Operation;
use Antares\Extension\Processors\Acl;
use Illuminate\Console\Command;
use Antares\Extension\Manager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class AclCommand extends Command implements OperationHandlerContract
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'extension:acl:reload {extension? : Extension full name}';

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
     * @param Acl $acl
     */
    public function __construct(Acl $acl)
    {
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
    public function handle(Manager $manager)
    {

        $extensionName = $this->argument('extension');
        if ($extensionName) {
            $extension = $manager->getAvailableExtensions()->findByName($extensionName);

            if ($extension instanceof ExtensionContract) {
                $this->acl->import($this, $extension, true);
            }
        } else {
            $extensions = $manager->getAvailableExtensions()->filterByActivated();

            foreach ($extensions as $extension) {
                $this->acl->import($this, $extension, true);
            }
        }
    }

    /**
     * @param Operation $operation
     * @return void
     */
    public function operationSuccess(Operation $operation)
    {
        $this->info($operation->getMessage());
        $this->line('');
    }

    /**
     * @param Operation $operation
     * @return void
     */
    public function operationFailed(Operation $operation)
    {
        $this->error($operation->getMessage());
        $this->line('');
    }

    /**
     * @param Operation $operation
     * @return void
     */
    public function operationInfo(Operation $operation)
    {
        $this->line($operation->getMessage());
    }

}

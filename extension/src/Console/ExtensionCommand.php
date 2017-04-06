<?php

declare(strict_types=1);

namespace Antares\Extension\Console;

use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Manager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Antares\Extension\Model\Operation;

abstract class ExtensionCommand extends Command implements OperationHandlerContract
{

	/**
	 * Extension Manager instance.
	 *
	 * @var Manager
	 */
	protected $manager;

	/**
	 * ExtensionCommand constructor.
	 * @param Manager $manager
	 */
	public function __construct(Manager $manager)
	{
		parent::__construct();

		$this->manager = $manager;
	}

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Extension Name.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
			['purge', null, InputOption::VALUE_NONE, 'Remove the extension using the composer remove command.'],
        ];
    }

    /**
     * @param Operation $operation
     * @return void
     */
	public function operationSuccess(Operation $operation) {
		$this->info($operation->getMessage());
	}

    /**
     * @param Operation $operation
     * @return void
     */
	public function operationFailed(Operation $operation) {
		$this->error($operation->getMessage());
	}

    /**
     * @param Operation $operation
     * @return void
     */
	public function operationInfo(Operation $operation) {
		$this->line($operation->getMessage());
	}

}

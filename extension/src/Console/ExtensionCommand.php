<?php

declare(strict_types=1);

namespace Antares\Extension\Console;

use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Manager;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
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
     * @var array
     */
    protected $validOptions = [
        'skip-composer',
    ];

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
     * @return array
     */
	protected function getValidOptions() : array {
        return Arr::only($this->options(), $this->validOptions);
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

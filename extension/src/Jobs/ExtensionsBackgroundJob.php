<?php

declare(strict_types=1);

namespace Antares\Extension\Jobs;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Contracts\OperationContract;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Outputs\OperationFileOutput;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Antares\Extension\Manager;

class ExtensionsBackgroundJob implements ShouldQueue {

    use Queueable, SerializesModels, InteractsWithQueue;

    /**
     * The extension name.
     *
     * @var string
     */
    protected $extensionName;

    /**
     * Operation class name.
     *
     * @var string
     */
    protected $operationClassName;

    /**
     * Output file name.
     *
     * @var string
     */
    protected $outputFileName;

    /**
     * Flags for the process.
     *
     * @var array
     */
    protected $flags = [];

    /**
     * ExtensionsBackgroundJob constructor.
     * @param string $extensionName
     * @param string $operationClassName
     * @param string $outputFileName
     * @param array $flags
     */
    public function __construct(string $extensionName, string $operationClassName, string $outputFileName, array $flags = []) {
        $this->extensionName        = $extensionName;
        $this->operationClassName   = $operationClassName;
        $this->outputFileName       = $outputFileName;
        $this->flags                = $flags;
    }

    /**
     * Handles the job.
     *
     * @param Manager $manager
     * @param Container $container
     * @throws \InvalidArgumentException
     * @throws ExtensionException
     * @throws FileNotFoundException
     * @throws \Exception
     */
    public function handle(Manager $manager, Container $container)
    {
        $output     = new OperationFileOutput($this->outputFileName);
        $operation  = $container->make($this->operationClassName);
        $extension  = $manager->getAvailableExtensions()->findByName($this->extensionName);

        if($extension instanceof ExtensionContract && $operation instanceof OperationContract) {
            $operation->run($output, $extension, $this->flags);
        }
    }

}

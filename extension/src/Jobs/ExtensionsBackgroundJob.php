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
     * Operations class names.
     *
     * @var string[]
     */
    protected $operationsClassNames;

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
     * @param array $operationsClassNames
     * @param string $outputFileName
     * @param array $flags
     */
    public function __construct(string $extensionName, array $operationsClassNames, string $outputFileName, array $flags = []) {
        $this->extensionName        = $extensionName;
        $this->operationsClassNames   = $operationsClassNames;
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
        $extension  = $manager->getAvailableExtensions()->findByName($this->extensionName);

        foreach($this->operationsClassNames as $operationClassName) {
            $operation = $container->make($operationClassName);

            if($extension instanceof ExtensionContract && $operation instanceof OperationContract) {
                $operation->run($output, $extension, $this->flags);

                if($output->failed()) {
                    return;
                }
            }
        }
    }

}

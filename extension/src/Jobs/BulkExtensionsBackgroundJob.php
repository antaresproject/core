<?php

declare(strict_types=1);

namespace Antares\Extension\Jobs;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Contracts\OperationContract;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Outputs\OperationFileOutput;
use Antares\Extension\Processors\Composer;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Antares\Extension\Manager;

class BulkExtensionsBackgroundJob implements ShouldQueue {

    use Queueable, SerializesModels, InteractsWithQueue;

    /**
     * Extensions names.
     *
     * @var array
     */
    protected $extensionsNames;

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
     * BulkExtensionsBackgroundJob constructor.
     * @param array $extensionsNames
     * @param string $operationClassName
     * @param string $outputFileName
     * @param array $flags
     */
    public function __construct(array $extensionsNames, string $operationClassName, string $outputFileName, array $flags = []) {
        $this->extensionsNames      = $extensionsNames;
        $this->operationClassName   = $operationClassName;
        $this->outputFileName       = $outputFileName;
        $this->flags                = $flags;
    }

    /**
     * Handles the job.
     *
     * @param Manager $manager
     * @param Container $container
     * @param Composer $composer
     * @throws \InvalidArgumentException
     * @throws ExtensionException
     * @throws FileNotFoundException
     * @throws \Exception
     */
    public function handle(Manager $manager, Container $container, Composer $composer)
    {
        $output     = new OperationFileOutput($this->outputFileName);
        $operation  = $container->make($this->operationClassName);

        $composer->run($output, $this->extensionsNames);

        if($output->failed()) {
            return;
        }

        $this->flags = array_merge($this->flags, ['skip-composer']);

        foreach($this->extensionsNames as $extensionName) {
            $extensionName  = explode(':', $extensionName)[0];
            $extension      = $manager->getAvailableExtensions()->findByName($extensionName);

            if($extension instanceof ExtensionContract && $operation instanceof OperationContract) {
                $operation->run($output, $extension, $this->flags);
            }
        }
    }

}

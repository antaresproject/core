<?php

declare(strict_types=1);

namespace Antares\Extension\Jobs;

use Antares\Extension\Outputs\OperationFileOutput;
use Antares\Extension\Processors\Composer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class BulkComposerJob implements ShouldQueue {

    use Queueable, SerializesModels, InteractsWithQueue;

    /**
     * Extensions names.
     *
     * @var string
     */
    protected $extensionsNames;

    /**
     * Output file name.
     *
     * @var string
     */
    protected $outputFileName;

    /**
     * BulkComposerJob constructor.
     * @param array $extensionsNames
     * @param string $outputFileName
     */
    public function __construct(array $extensionsNames, string $outputFileName) {
        $this->extensionsNames  = $extensionsNames;
        $this->outputFileName   = $outputFileName;
    }

    /**
     * Handles the job.
     *
     * @param Composer $composer
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function handle(Composer $composer)
    {
        $output = new OperationFileOutput($this->outputFileName);

        $composer->run($output, $this->extensionsNames);
    }

}

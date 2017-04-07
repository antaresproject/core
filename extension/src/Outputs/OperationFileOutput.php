<?php

declare(strict_types=1);

namespace Antares\Extension\Outputs;

use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Symfony\Component\Console\Output\StreamOutput;
use Antares\Extension\Model\Operation;

class OperationFileOutput implements OperationHandlerContract  {

    /**
     * @var StreamOutput
     */
    protected $output;

    /**
     * Failed operations.
     *
     * @var int
     */
    protected $failed = 0;

    /**
     * OperationFileOutput constructor.
     * @param string $filePath
     * @throws \InvalidArgumentException
     */
    public function __construct(string $filePath) {
        $this->output = new StreamOutput( fopen($filePath, 'ab') );
    }

    /**
     * @return StreamOutput
     */
    public function getStream() : StreamOutput {
        return $this->output;
    }

    /**
     * @param Operation $operation
     * @return void
     */
    public function operationSuccess(Operation $operation) {
        $this->output->writeln( $this->getNormalizedContent($operation->getMessage()) );
    }

    /**
     * @param Operation $operation
     * @return void
     */
    public function operationFailed(Operation $operation) {
        $this->output->writeln( $this->getNormalizedContent($operation->getMessage()) );
        $this->failed++;
    }

    /**
     * @param Operation $operation
     * @return void
     */
    public function operationInfo(Operation $operation) {
        $this->output->writeln( $this->getNormalizedContent($operation->getMessage()) );
    }

    /**
     * @return bool
     */
    public function failed() {
        return $this->failed > 0;
    }

    /**
     * @param string $content
     * @return string
     */
    protected function getNormalizedContent(string $content) : string {
        $content .= "\r\n";
        $content = preg_replace('/[\s]{2,}/mu', '', $content);

        return $content;
        //$content = preg_replace("/[\x08]{2,}/mu", '', $content);

        //return str_replace("\x08", ' ', $content);
    }

}
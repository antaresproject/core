<?php
/**
 * Created by PhpStorm.
 * User: Marcin Kozak
 * Date: 2017-03-31
 * Time: 15:36
 */

namespace Antares\Extension\Events;

use Antares\Contracts\Events\EventContract;
use Exception;

class ComposerFailed implements EventContract {

    /**
     * @var string
     */
    public $command;

    /**
     * @var Exception
     */
    public $exception;

    /**
     * ComposerFailed constructor.
     * @param string $command
     * @param Exception $exception
     */
    public function __construct(string $command, Exception $exception) {
        $this->command      = $command;
        $this->exception    = $exception;
    }

}

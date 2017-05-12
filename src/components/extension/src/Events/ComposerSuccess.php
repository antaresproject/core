<?php
/**
 * Created by PhpStorm.
 * User: Marcin Kozak
 * Date: 2017-03-31
 * Time: 15:36
 */

namespace Antares\Extension\Events;

use Antares\Contracts\Events\EventContract;

class ComposerSuccess implements EventContract {

    /**
     * @var string
     */
    public $command;

    /**
     * ComposerSuccess constructor.
     * @param string $command
     */
    public function __construct(string $command) {
        $this->command = $command;
    }

}

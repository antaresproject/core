<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Events\Composer;

use Antares\Foundation\Events\AbstractEvent;

class Failed extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Composer: Command failed';

    /** @var string */
    protected static $description = 'Runs after composer command failed';

    /** @var string */
    public $command;

    /** @var \Exception */
    public $exception;

    /**
     * Failed constructor
     *
     * @param string     $command
     * @param \Exception $extension
     */
    public function __construct(string $command, \Exception $extension)
    {
        $this->command = $command;
        $this->exception = $extension;

        parent::__construct();
    }

}

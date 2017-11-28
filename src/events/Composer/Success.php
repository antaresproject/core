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

class Success extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Composer: Command succeeded';

    /** @var string */
    protected static $description = 'Runs after composer command succeeded';

    /** @var string */
    public $command;

    /**
     * Success constructor
     *
     * @param string $command
     */
    public function __construct(string $command)
    {
        $this->command = $command;

        parent::__construct();
    }

}

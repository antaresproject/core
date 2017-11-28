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

namespace Antares\Events\SystemReady;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Memory\Provider;

class AntaresStarted extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Started';

    /** @var string */
    protected static $description = 'Runs after Antares started';

    /** @var Provider */
    public $memory;

    /**
     * AntaresStarted constructor
     *
     * @param Provider $memory
     */
    public function __construct(Provider $memory)
    {
        $this->memory = $memory;

        parent::__construct();
    }

}

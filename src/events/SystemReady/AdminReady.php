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

class AdminReady extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares Ready: Admin';

    /** @var string */
    protected static $description = 'Runs after Antares is ready';

    /**
     * AdminReady constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

}

<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Events;

use Antares\Updater\Contracts\RedAlert as RedAlertContract;
use Antares\Support\Facades\Memory;

class SandboxMode
{

    /**
     * alert box instance
     *
     * @var RedAlertContract 
     */
    protected $alert;

    /**
     * constructing
     * 
     * @param RedAlertContract $alert
     */
    public function __construct(RedAlertContract $alert)
    {
        $this->alert = $alert;
    }

    /**
     * fire event
     */
    public function handle()
    {
        if (!(int) Memory::make('primary')->get('sandbox.mode')) {
            return false;
        }
    }

}

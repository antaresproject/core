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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Notifications\Console;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Antares\Notifications\Model\NotificationSeverity;

class NotificationSeveritiesCommand extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'notifications:severity-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show list of available notification severity';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $severities = NotificationSeverity::orderBy('id', 'asc')->get();
        $flatten    = [];
        foreach ($severities as $severity) {
            $flatten[] = ['<info>' . $severity->id . '</info>', '<fg=red>' . $severity->name . '</fg=red>'];
        }

        if (count($flatten) > 0) {
            $this->table(['Id', 'Name'], $flatten);
        } else {
            $this->error('No severities found');
        }
    }

}

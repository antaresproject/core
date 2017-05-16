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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Console;

use Antares\Notifications\Model\NotificationsStack;
use Illuminate\Support\Facades\DB;
use Antares\View\Console\Command;

class NotificationsRemover extends Command
{

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Notifications remover';

    /**
     * when command should be executed
     *
     * @var String
     */
    protected $launched = 'daily';

    /**
     * Name of default category automation command
     *
     * @var String
     */
    protected $category = 'custom';

    /**
     * when command can be executed
     *
     * @var array
     */
    protected $availableLaunches = [
        'everyFiveMinutes',
        'everyTenMinutes',
        'everyThirtyMinutes',
        'hourly',
        'daily'
    ];

    /**
     * The console command name.
     *
     * @var String
     */
    protected $name = 'notifications:remove-olds';

    /**
     * The console command description.
     *
     * @var String
     */
    protected $description = 'Remove notification logs after days configured in general settings section.';

    /**
     * Builder instance
     *
     * @var \Illuminate\Database\Eloquent\Builder 
     */
    protected $builder;

    /**
     * Construct
     * 
     * @param NotificationsStack $model
     */
    public function __construct(NotificationsStack $model)
    {
        parent::__construct();
        $this->builder = $model->newQuery();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $days = (int) app('antares.memory')->make('primary')->get('notifications_remove_after_days', '');
        if (!$days) {
            $this->comment('Configuration for remove notifications logs is not configured. Ignoring.');
            return;
        }
        $logs  = $this->builder->where('created_at', 'like', DB::raw("DATE(DATE_ADD(NOW(), INTERVAL +{$days} DAY))"));
        $count = $logs->count();
        if (!$count) {
            $this->comment('There are no notification logs available. Ignoring.');
            return;
        }
        $logs->delete();
        $this->line(sprintf('%d notification logs has been deleted.', $count));
    }

}

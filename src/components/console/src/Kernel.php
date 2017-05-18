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
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Console;

use Antares\Automation\Model\Jobs;

class Kernel extends BaseKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\Inspire',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (!extension_active('automation')) {
            return false;
        }
        $queue = Jobs::all();
        foreach ($queue as $job) {
            if (!(int) $job->active) {
                continue;
            }
            $launch = $job->value['launch'];
            if (!is_array($launch)) {
                if (!is_string($launch)) {
                    continue;
                }
                $schedule->command($job->name)->$launch();
            } else {
                $command = $schedule->command($job->name);
                foreach ($launch as $method => $params) {
                    call_user_func_array(array($command, $method), $params);
                }
            }
        }
    }

}

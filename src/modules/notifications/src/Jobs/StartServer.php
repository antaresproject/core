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


namespace Antares\Notifications\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Symfony\Component\Process\Process;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class StartServer implements ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        Queueable;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $artisan = base_path('artisan');
        $process = new Process("php {$artisan} notifications:start");
        $process->start();
    }

}

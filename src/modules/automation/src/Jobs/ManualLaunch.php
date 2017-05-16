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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;
use Illuminate\Queue\InteractsWithQueue;
use Antares\Automation\Repository\Reports;
use Illuminate\Contracts\Queue\ShouldQueue;

class ManualLaunch implements ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        Queueable;

    /**
     * command to lanuch
     *
     * @var String 
     */
    protected $command = null;

    /**
     * command setter
     * 
     * @param String $command
     * @return \Antares\Automation\Jobs\ManualLaunch
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);
        $before  = microtime(true);
        $artisan = base_path('artisan');
        $command = $this->command;
        $process = new Process("php {$artisan} {$command}");
        $process->setTimeout(4000);
        $process->run();
        $after   = microtime(true);
        $runtime = $after - $before;
        app(Reports::class)->saveReport($command, $runtime, $process);
    }

    /**
     * Command name getter
     * 
     * @return String
     */
    public function getCommand(): String
    {
        return $this->command;
    }

}

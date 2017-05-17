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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Antares\View\Console\Command;
use function config;

class QueueCommand extends Command
{

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Queue Daemon';

    /**
     * when command should be executed
     *
     * @var String
     */
    protected $launched = 'everyFiveMinutes';

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
     * Name of automation command category 
     *
     * @var String
     */
    protected $category = 'system';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'queue:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue daemon.';

    /**
     * command definition
     *
     * @var String 
     */
    protected $command = 'php artisan queue:work';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->isCli()) {
            $artisan = base_path('artisan');
            while (true) {
                set_time_limit(0);
                ini_set('max_execution_time', 0);
                ignore_user_abort();
                $process = new Process("php {$artisan} queue:work database --queue email,sms,install");
                $process->run();
                echo ($process->isSuccessful()) ? $process->getOutput() : $process->getErrorOutput();
                unset($process);
                sleep(1);
            }
        } else {
            foreach ($this->getQueues() as $queue) {
                Artisan::call('queue:work', $this->getParams($queue));
            }
            $this->info('Queue command finished.');
        }
    }

    /**
     * build daemon execution command
     * 
     * @return String
     */
    protected function getCommand()
    {
        $params = $this->getParams();
        return implode(' ', [$this->command, implode(' ', $params)]);
    }

    /**
     * queues getter
     * 
     * @return array
     */
    protected function getQueues()
    {
        return config('queue.queues', []);
    }

    /**
     * get params depends on queues and execution source
     * 
     * @return String
     */
    protected function getParams($name = null)
    {
        $queues = !is_null($name) ? [$name] : $this->getQueues();
        $params = [
            config('queue.default'),
        ];
        if (empty($queues)) {
            return $params;
        }
        $queue = implode(',', $queues);
        if ($this->isCli()) {
            array_push($params, '--queue ' . $queue);
        } else {
            $params['--queue'] = $queue;
        }

        unset($queues);


        return $params;
    }

    /**
     * whether command runs from cli
     * 
     * @return boolean
     */
    protected function isCli()
    {
        return php_sapi_name() == 'cli';
    }

}

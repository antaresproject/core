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



namespace Antares\Automation\Console;

use Illuminate\Support\Facades\Event as SupportEvent;
use Antares\Automation\Repository\Reports;
use Illuminate\Support\Facades\Artisan;
use Antares\Console\Scheduling\Event;
use Antares\View\Console\Command;
use Antares\Console\Schedule;

class QueueCommand extends Command
{

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Queue Automation Daemon';

    /**
     * when command should be executed
     *
     * @var String
     */
    protected $launched = 'everyMinute';

    /**
     * when command can be executed
     *
     * @var array
     */
    protected $availableLaunches = [
        'everyMinute',
        'everyFiveMinutes',
        'everyTenMinutes',
        'everyThirtyMinutes',
        'hourly',
        'daily'
    ];

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'automation:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue Automation Daemon.';

    /**
     * The schedule instance.
     *
     * @var Schedule
     */
    protected $schedule;

    /**
     * Reports model instance
     *
     * @var Reports
     */
    protected $reports;

    /**
     * Ignored commands by automation
     *
     * @var array 
     */
    protected $ignored = [];

    /**
     * Construct
     *
     * @param  Schedule  $schedule
     * @param  Reports  $reports
     */
    public function __construct(Schedule $schedule, Reports $reports)
    {
        $this->schedule = $schedule;
        $this->reports  = $reports;
        $this->ignored  = config('antares/automation::ignored');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->isCli()) {
            while (true) {
                set_time_limit(0);
                ini_set('max_execution_time', 0);
                ignore_user_abort();
                $this->runScheduledCommands();
            }
        } else {
            Artisan::call('automation:run');
            $this->info('Queue command finished.');
        }
    }

    /**
     * Runs scheduled commands
     */
    protected function runScheduledCommands()
    {
        $events    = $this->schedule->dueEvents($this->laravel);
        $eventsRan = 0;
        if (empty($events)) {
            $this->info('Events list is empty.');
        }
        $this->comment(sprintf('Start rejecting of %d commands.', count($events)));
        foreach ($events as $event) {

            if (!$event->filtersPass($this->laravel)) {
                $this->comment(sprintf('Command %s cannot pass by filter. Ignoring...', $event->command));
                continue;
            }
            $this->runSingleCommand($event);
            ++$eventsRan;
        }

        if (count($events) === 0 || $eventsRan === 0) {
            $this->info('No scheduled commands are ready to run.');
        }
        sleep(60);
    }

    /**
     * Runs single automation command
     * 
     * @param Event $event
     */
    protected function runSingleCommand(Event $event)
    {

        $commandName = $this->schedule->rejectCommand($event->command);
        SupportEvent::fire('antares.automation', 'before.' . $commandName);
        if (!$this->isIgnored($commandName)) {
            $this->info(sprintf('Command %s is ignored. Continue...', $commandName));
            return false;
        }

        $this->line('<info>Running scheduled command:</info> ' . $event->getSummaryForDisplay());
        $before  = microtime(true);
        $event->run($this->laravel);
        $after   = microtime(true);
        $process = $event->getProcess();
        $runtime = $after - $before;
        ($process->isSuccessful()) ? $this->comment(sprintf('Command has been completed successfully in time %d and outputs %s.', $runtime, $process->getOutput())) : $this->comment('Command failed: ' . $process->getErrorOutput());

        $this->reports->saveReport($commandName, $runtime, $process);
        $this->comment(sprintf('Report has been saved.'));
        SupportEvent::fire('antares.automation', 'after.' . $commandName);
    }

    /**
     * Whether command is valid
     * 
     * @param String $command
     * @return boolean
     */
    protected function isIgnored($command)
    {
        foreach ($this->ignored as $ignored) {
            if (str_contains($command, $ignored)) {
                return false;
            }
        }
        return true;
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

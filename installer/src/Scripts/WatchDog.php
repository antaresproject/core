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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Installation\Scripts;

use Antares\Config\Repository;
use Exception;

class WatchDog
{

    /**
     * available commands
     *
     * @var array 
     */
    protected $commands = [];

    /**
     * Adapter instance
     *
     * @var type 
     */
    protected $adapter;

    /**
     * constructing
     * 
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->commands = $config->get('antares/installer::post_installation_commands', []);
        if ($this->isWin()) {
            $adapter       = new WindowsProcessMonitor;
            $adapter->setCommands($this->commands);
            $this->adapter = $adapter;
        } else {
            $this->adapter = new UnixProcessMonitor;
        }
    }

    /**
     * Creates command to run
     * 
     * @param String $command
     * @return String
     */
    protected function createCommand($command)
    {
        $artisan = base_path('artisan');
        return "php {$artisan} $command";
    }

    /**
     * checks whether we need to use windows commands
     *
     * @return boolean
     */
    protected function isWin()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    /**
     * Runs process monitor
     * 
     * @return \Antares\Installation\Scripts\WatchDog
     */
    public function run()
    {
        try {
            foreach ($this->commands as $command) {
                $this->adapter->process($this->createCommand($command));
            }
        } catch (Exception $ex) {
            app('antares.notifier')
                    ->setMessage(sprintf('Unable to start monitor process. Exception message: %s with code: %d', [$ex->getMessage(), $ex->getCode()]))
                    ->alert('high')
                    ->severity('warning');
        }

        return $this;
    }

    /**
     * start custom process
     * 
     * @param String $name
     * @return boolean
     */
    public function up($name)
    {
        try {
            if (self::isWin()) {
                return false;
            }
            if (!app('antares.installed')) {
                return false;
            }
            $processes = null;
            exec('ps aux|grep php', $processes);
            $started   = false;
            foreach ($processes as $process) {
                if (str_contains($process, $name)) {
                    $started = true;
                }
            }
            ignore_user_abort();
            $log = storage_path('logs' . DIRECTORY_SEPARATOR . snake_case($name) . '.log');
            if (!$started) {
                $artisan = base_path('artisan');
                shell_exec("php {$artisan} $name >> " . $log . " &");
                return true;
            }
        } catch (Exception $ex) {
            return false;
        }
    }

}

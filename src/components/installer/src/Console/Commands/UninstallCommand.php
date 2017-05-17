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


namespace Antares\Installation\Console\Commands;

use Antares\Installation\Contracts\UninstallListener;
use Illuminate\Console\Command;
use Antares\Installation\Processor\Uninstaller;

class UninstallCommand extends Command implements UninstallListener
{

    protected $signature   = 'antares:uninstall';
    protected $description = 'Perform an uninstall process of the Antares Framework.';

    /**
     * @var Uninstaller
     */
    protected $uninstaller;

    public function __construct(Uninstaller $uninstaller)
    {
        parent::__construct();

        $this->uninstaller = $uninstaller;
    }

    /**
     * Handles the command task.
     * @throws \Exception
     */
    public function handle()
    {
        if ($this->confirm('The Database will be flushed. Do you want to uninstall?')) {
            $this->uninstaller->truncateTables($this);
            $this->uninstaller->flushCacheAndSession($this);
        }
    }

    /**
     * Listener for success uninstall process.
     *
     * @param string $msg
     * @return mixed
     */
    public function uninstallSuccess($msg)
    {
        $this->info($msg);
    }

    /**
     * Listener for failed uninstall process.
     *
     * @param string $msg
     * @return mixed
     */
    public function uninstallFailed($msg)
    {
        $this->error($msg);
    }

}

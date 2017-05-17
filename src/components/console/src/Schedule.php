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

use Illuminate\Console\Scheduling\Schedule as SupportSchedule;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\ProcessUtils;
use Antares\Console\Scheduling\Event;

class Schedule extends SupportSchedule
{

    /**
     * reject Artisan command
     *
     * @param  string  $command
     * @return \Illuminate\Console\Scheduling\Event
     */
    public function rejectCommand($command)
    {
        $binary = ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false));
        if (defined('HHVM_VERSION')) {
            $binary .= ' --php';
        }

        if (defined('ARTISAN_BINARY')) {
            $artisan = ProcessUtils::escapeArgument(ARTISAN_BINARY);
        } else {
            $artisan = 'artisan';
        }
        return trim(str_replace([$binary, $artisan], '', $command));
    }

}

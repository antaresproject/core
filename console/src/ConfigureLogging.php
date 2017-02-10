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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Console;

use Illuminate\Foundation\Bootstrap\ConfigureLogging as IlluminateConfigureLogging;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\Writer;

class ConfigureLogging extends IlluminateConfigureLogging
{

    /**
     * {@inheritdoc}
     */
    protected function configureSingleHandler(Application $app, Writer $log)
    {
        $log->useFiles($app->storagePath() . '/logs/' . $this->getFilename());
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDailyHandler(Application $app, Writer $log)
    {
        $log->useDailyFiles(
                $app->storagePath() . '/logs/' . $this->getFilename(), $app->make('config')->get('app.log_max_files', 5)
        );
    }

    /**
     * Gets log filename depends on run source
     * 
     * @return String
     */
    protected function getFilename()
    {
        return 'laravel' . ($this->isCli() ? '-cli' : '') . '.log';
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

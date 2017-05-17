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


namespace Antares\Config\Console;

use Symfony\Component\Finder\Finder;
use Illuminate\Foundation\Console\ConfigCacheCommand as BaseCommand;

class ConfigCacheCommand extends BaseCommand
{

    /**
     * Boot a fresh copy of the application configuration.
     *
     * @return array
     */
    protected function getFreshConfiguration()
    {
        $app = require $this->laravel['path.base'] . '/bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $files = array_merge(
                $app->make('config')->get('compile.config', []), $this->getConfigurationFiles()
        );

        foreach ($files as $file) {
            $app->make('config')->get($file);
        }

        return $app->make('config')->all();
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @return array
     */
    protected function getConfigurationFiles()
    {
        $files = [];
        $path  = $this->laravel->configPath();
        $found = Finder::create()->files()->name('*.php')->depth('== 0')->in($path);

        foreach ($found as $file) {
            $files[] = basename($file->getRealPath(), '.php');
        }

        return $files;
    }

}

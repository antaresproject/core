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


namespace Antares\Extension\Console;

class ComposerCommand extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'composer:checkout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Composer checkout command';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->getStatus()) {
            $this->downloadComposer();
        } else {
            $this->info('Composer installed');
        }
    }

    /**
     * verify whether composer installed
     * 
     * @return boolean
     */
    protected function getStatus()
    {
        /* @var $filesystem \Illuminate\Filesystem\Filesystem */
        $filesystem = app(\Illuminate\Filesystem\Filesystem::class);
        foreach (['composer.phar', 'installer.php'] as $filename) {
            if (!$filesystem->exists(base_path($filename))) {
                return false;
            }
        }
        return true;
    }

    /**
     * download composer
     */
    protected function downloadComposer()
    {
        $installerURL  = 'https://getcomposer.org/installer';
        $installerFile = base_path('installer.php');
        if (!file_exists($installerFile)) {
            $this->info('Downloading ' . $installerURL . PHP_EOL);
            flush();
            $ch = curl_init($installerURL);
            curl_setopt($ch, CURLOPT_CAINFO, config_path('cacert.pem'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_FILE, fopen($installerFile, 'w+'));
            if (curl_exec($ch))
                $this->info('Success downloading ' . $installerURL . PHP_EOL);
            else {
                $this->info('Error downloading ' . $installerURL . PHP_EOL);
                die();
            }
            flush();
        }
        $this->info('Installer found : ' . $installerFile . PHP_EOL);
        $this->info('Starting installation...' . PHP_EOL);
        flush();
        $argv = array();
        include $installerFile;
        flush();
    }

}

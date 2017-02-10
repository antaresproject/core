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

class ComposerDumpautoloadCommand extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'composer:dumpautoload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Composer dumpautoload command';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        \Illuminate\Support\Facades\Artisan::call('composer:checkout');
        $this->extractComposer();
        $this->command('dump-autoload');
        /* @var $filesystem \Illuminate\Filesystem\Filesystem */
        $filesystem = app(\Illuminate\Filesystem\Filesystem::class);
        $installer  = base_path('installer.php');
        if ($filesystem->exists($installer)) {
            $filesystem->delete($installer);
        }
        $extracted = base_path('extraced');
        if ($filesystem->exists($extracted)) {
            $filesystem->deleteDirectory($extracted);
        }
    }

    protected function extractComposer()
    {
        /* @var $filesystem \Illuminate\Filesystem\Filesystem */
        $filesystem = app(\Illuminate\Filesystem\Filesystem::class);
        if ($filesystem->exists(base_path('extracted'))) {
            return;
        }
        if (file_exists(base_path('composer.phar'))) {
            echo 'Extracting composer.phar ...' . PHP_EOL;
            flush();
            $composer = new \Phar(base_path('composer.phar'));
            $composer->extractTo(base_path('extracted'));
            echo 'Extraction complete.' . PHP_EOL;
        } else
            echo 'composer.phar does not exist';
    }

    protected function command($command)
    {
        set_time_limit(-1);
        putenv('COMPOSER_HOME=' . base_path('extracted/bin/composer'));
        if (!file_exists(base_path('extracted'))) {
            $this->extractComposer();
        }
        require_once(base_path('extracted/vendor/autoload.php'));
        $input  = new \Symfony\Component\Console\Input\StringInput('dump-autoload');
        $output = new \Symfony\Component\Console\Output\StreamOutput(fopen('php://output', 'w'));
        $app    = new \Composer\Console\Application();
        
    }

}

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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Updater\Console;

/**
 * Based on https://github.com/spatie/laravel-backup
 * 
 * @author Spatie
 * @modifier Łukasz Cirut
 */
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Filesystem\Filesystem;
use Antares\Updater\Model\Backup;
use Antares\View\Console\Command;
use Illuminate\Bus\Queueable;

class AppBackupCommand extends Command implements ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        Queueable;

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Application Backup Command';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'backup:app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates application backup';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:app {id?}';

    /**
     * when command should be executed
     *
     * @var String
     */
    protected $launched = 'daily';

    /**
     * when command can be executed
     *
     * @var array
     */
    protected $availableLaunches = [
        'daily'
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(KernelContract $kernelConsole)
    {
        if (!is_null($id = $this->argument('id'))) {
            $model         = Backup::query()->findOrFail($id);
            $model->status = 'in_progress';
            $model->save();
        } else {
            $model = new Backup([
                'version' => app('antares.version')->getAdapter()->getVersion(),
                'status'  => 'in_progress'
            ]);
        }
        $kernelConsole->call('backup:run');
        $files  = app(Filesystem::class);
        $target = config('laravel-backup.destination.path');
        $file   = last($files->allFiles(storage_path("app/{$target}")));
        $model->fill([
            'name'   => $file->getFilename(),
            'path'   => str_replace(base_path(), '', $file->getRealPath()),
            'status' => 'completed'
        ]);
        $model->save();

        $this->line($kernelConsole->output());
    }

}

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

use Antares\Updater\Contracts\DatabaseRestorator as SupportDatabaseRestorator;
use Antares\Updater\Contracts\FilesRestorator;
use Antares\Updater\Contracts\Decompressor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Filesystem\Filesystem;
use Antares\View\Console\Command;
use Antares\Updater\Model\Backup;
use Illuminate\Bus\Queueable;
use Exception;

class RestoreAppCommand extends Command implements ShouldQueue
{

    use InteractsWithQueue,
        SerializesModels,
        Queueable;

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Application Restore Command';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'backup:restore';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore application from backup';

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
     * @param Filesystem $filesystem
     * @param Decompressor $decompressor
     * @param SupportDatabaseRestorator $dbRestorator
     * @param FilesRestorator $filesRestorator
     * @throws Exception
     */
    public function handle(Filesystem $filesystem, Decompressor $decompressor, SupportDatabaseRestorator $dbRestorator, FilesRestorator $filesRestorator)
    {

        try {
            $name  = $this->argument('name');
            $model = app(Backup::class)->where('name', $name)->firstOrFail();
            $path  = base_path($model->path);
            $this->line(trans('antares/updater::commands.starting_restoration', ['name' => $name]));
            if (!$filesystem->exists($path)) {
                throw new Exception('Unable to find valid backup path.');
            }
            $decompressedPath = $decompressor->decompress($path);
            $dbRestorator->prepare($decompressedPath)->create()->copy()->dump()->drop();
            $filesRestorator->prepare($decompressedPath)->restore();
            $this->line(trans('antares/updater::commands.finishing_restoration'));
            $this->line(trans('antares/updater::commands.app_restore_completed', ['name' => $name]));
        } catch (Exception $ex) {
            $this->error($ex->getMessage());
        }
    }

}

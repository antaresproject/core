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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Console;

use Symfony\Component\Console\Helper\ProgressBar;
use Illuminate\Database\Eloquent\Collection;
use Antares\Translations\Models\Languages;
use Antares\Logger\Model\LogsTranslations;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Antares\View\Console\Command;
use Antares\Logger\Model\Logs;
use Exception;

class LogsTranslationSynchronizer extends Command
{

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Logs Translation Synchronizer';

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
    protected $name = 'logs:synchronize-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize logger message translations.';

    /**
     * whether command can be disabled
     *
     * @var boolean 
     */
    protected $disablable = false;

    /**
     * Languages container
     *
     * @var Languages 
     */
    protected $langs = null;

    /**
     * Log translations container
     *
     * @var LogsTranslations 
     */
    protected $translations = null;

    /**
     * Logs container
     *
     * @var Logs
     */
    protected $logs = null;

    /**
     * Construct
     * 
     * @param Languages $langs
     * @param LogsTranslations $translations
     * @param Logs $logs
     */
    public function __construct(Languages $langs, LogsTranslations $translations, Logs $logs)
    {
        $this->langs        = $langs;
        $this->translations = $translations;
        $this->logs         = $logs;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();
        ini_set('memory_limit', '256M');
        try {

            $this->comment('Clearing all logs translations...');
            $this->translations->delete();
            $this->comment('Logs translations has been cleared.');

            $this->comment('Preparing to indexing log items...');

            $count    = $this->logs->count();
            $progress = new ProgressBar($this->getOutput(), $count);
            $progress->start();

            $langs = $this->langs->all();


            $this->logs->chunk(10000, function($elements) use ($progress, $langs) {
                foreach ($langs as $lang) {
                    $this->translateAndSave($elements, $lang, $progress);
                }
            });
            $progress->finish();
            $this->line("\nLogger messages has been translated and saved.");
        } catch (Exception $ex) {
            $this->error($ex->getMessage());
            DB::rollback();
        }
        DB::commit();
    }

    /**
     * Translates and saves log
     * 
     * @param Collection $elements
     * @param Languages $lang
     * @param ProgressBar $progress
     * @return void
     */
    protected function translateAndSave(Collection $elements, Languages $lang, ProgressBar $progress)
    {
        foreach ($elements as $element) {
            $model = $element->translation()->getModel()->firstOrNew(['lang_id' => $lang->id, 'log_id' => $element->id]);
            try {
                $translated  = $element->translated($lang->code);
                $model->raw  = strip_tags($translated);
                $model->text = $translated;
                $model->save();
            } catch (Exception $ex) {
                $this->error($ex->getMessage());
                Log::alert($ex);
                continue;
            }
            $progress->advance();
        }
        return;
    }

}

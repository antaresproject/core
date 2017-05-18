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


namespace Antares\View\Console;

use InvalidArgumentException;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\Console\Command as BaseCommand;

class OptimizeCommand extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pre-cache themes views in the application.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Compiling views');

        $this->compileViews();
    }

    /**
     * Compile all view files.
     *
     * @return void
     */
    protected function compileViews()
    {
        foreach ($this->laravel['view']->getFinder()->getPaths() as $path) {
            $this->compileViewsInPath($path);
        }
    }

    /**
     * Compile all views files in path.
     *
     * @param  string  $path
     *
     * @return void
     */
    protected function compileViewsInPath($path)
    {
        foreach ($this->laravel['files']->allFiles($path) as $file) {
            try {
                $engine = $this->laravel['view']->getEngineFromPath($file);
            } catch (InvalidArgumentException $e) {
                Log::emergency($e);
                continue;
            }

            $this->compileViewFile($engine, $file);
        }
    }

    /**
     * Compile single view file.
     *
     * @param  mixed  $engine
     * @param  string  $file
     *
     * @return void
     */
    protected function compileSingleViewFile($engine, $file)
    {
        if ($engine instanceof CompilerEngine) {
            $engine->getCompiler()->compile($file);
        }
    }

}

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


namespace Antares\Foundation\Console\Commands;

use PDOException;
use Illuminate\Console\Command;
use Antares\Contracts\Memory\Provider;
use Antares\Contracts\Foundation\Foundation;

class AssembleCommand extends Command
{

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'antares:assemble
        {--no-cache : Avoid running route and config caching.}
        {--no-optimize : Avoid running class optimization.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh application setup (during composer install/update)';

    /**
     * The application foundation implementation.
     *
     * @var \Antares\Contracts\Foundation\Foundation
     */
    protected $foundation;

    /**
     * The memory provider implementation.
     *
     * @var \Antares\Contracts\Memory\Provider
     */
    protected $memory;

    /**
     * Construct a new command.
     *
     * @param  \Antares\Contracts\Foundation\Foundation  $foundation
     * @param  \Antares\Contracts\Memory\Provider  $memory
     */
    public function __construct(Foundation $foundation, Provider $memory)
    {
        $this->foundation = $foundation;
        $this->memory     = $memory;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->setupApplication();

        $this->refreshApplication();

        $this->optimizeApplication();
    }

    /**
     * Refresh application for Antares.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        if (!$this->foundation->installed()) {
            return;
        }

        $this->call('extension:detect', ['--quiet' => true]);

        $extensions = $this->memory->get('extensions.active', []);

        try {
            foreach ($extensions as $extension => $config) {
                $options = ['name' => $extension, '--force' => true];

                $this->call('extension:refresh', $options);
                $this->call('extension:update', $options);
            }

            $this->foundation->make('antares.extension.provider')->writeFreshManifest();
        } catch (PDOException $e) {
            // Skip if application is unable to make connection to the database.
        }
    }

    /**
     * Setup application for Antares.
     *
     * @return void
     */
    protected function setupApplication()
    {
        $this->call('publish:assets', ['package' => 'antares/foundation']);
    }

    /**
     * Optimize application for Antares.
     *
     * @return void
     */
    protected function optimizeApplication()
    {
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('clear-compiled');

        if ($this->laravel->environment('production') && !$this->option('no-cache')) {
            $this->call('config:cache');
            $this->call('route:cache');
        }

        if (!$this->option('no-optimize')) {
            $this->call('antares:optimize');
        }
    }

}

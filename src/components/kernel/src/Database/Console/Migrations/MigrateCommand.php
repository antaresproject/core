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


namespace Antares\Database\Console\Migrations;

use Symfony\Component\Console\Input\InputOption;
use Illuminate\Database\Console\Migrations\MigrateCommand as BaseCommand;

class MigrateCommand extends BaseCommand
{

    /**
     * The path to the packages directory (vendor).
     *
     * @var string
     */
    protected $packagePath;

    /**
     * Set package path.
     *
     * @param  string  $packagePath
     *
     * @return $this
     */
    public function setPackagePath($packagePath)
    {
        $this->packagePath = $packagePath;

        return $this;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $this->prepareDatabase();

        $pretend = $this->input->getOption('pretend');
        $path    = $this->getMigrationPath();
        $this->migrator->run($path, [
            'pretend' => $pretend,
            'step'    => $this->input->getOption('step'),
        ]);

        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }

        if ($this->input->getOption('seed')) {
            $this->call('db:seed', ['--force' => true]);
        }
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        $path = $this->input->getOption('path');

        if (!is_null($realPath = $this->input->getOption('realpath'))) {
            return $realPath;
        }

        if (!is_null($package = $this->input->getOption('package'))) {
            is_null($path) && $path = 'resources/migrations';

            return $this->packagePath . '/' . $package . '/' . $path;
        }

        if (!is_null($path)) {
            return $this->laravel['path.base'] . '/' . $path;
        }

        return $this->laravel['path.database'] . '/migrations';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['realpath', null, InputOption::VALUE_OPTIONAL, 'The absolute path to migration files.', null],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to migration files.', null],
            ['package', null, InputOption::VALUE_OPTIONAL, 'The package to migrate.', null],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually.'],
        ];
    }

}

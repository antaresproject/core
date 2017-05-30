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

namespace Antares\Publisher;

use Antares\Extension\Manager;
use Illuminate\Database\Seeder as IlluminateSeeder;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Migrations\Migrator;
use Antares\Contracts\Publisher\Publisher;
use Illuminate\Support\Str;
use SplFileInfo;
use stdClass;

class MigrateManager implements Publisher
{

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * seeder instance.
     *
     * @var IlluminateSeeder
     */
    protected $seeder;

    /**
     * Extensions manager instance.
     *
     * @var Manager
     */
    protected $manager;

    /**
     * Construct a new instance.
     * 
     * @param Container $app
     * @param Migrator $migrator
     * @param IlluminateSeeder $seeder
     */
    public function __construct(Container $app, Migrator $migrator, IlluminateSeeder $seeder)
    {
        $this->app      = $app;
        $this->migrator = $migrator;
        $this->seeder   = $seeder;
        $this->manager  = app()->make(Manager::class);
    }

    /**
     * Create migration repository if it's not available.
     *
     * @return void
     */
    protected function createMigrationRepository()
    {
        $repository = $this->migrator->getRepository();
        if (!$repository->repositoryExists()) {
            $repository->createRepository();
        }
    }

    /**
     * Run migration for an extension or application.
     *
     * @param  string  $path
     *
     * @return void
     */
    public function run($path)
    {

        $this->createMigrationRepository();
        $repository = $this->migrator->getRepository();
        $migrations = $this->migrator->getMigrationFiles($path);
        $ran        = $repository->getRan();
        if (!empty($migrations)) {
            $files = array_keys($migrations);
            foreach ($files as $name) {
                if (!in_array($name, $ran)) {
                    continue;
                }
                $migration            = new stdClass();
                $migration->migration = $name;
                $repository->delete($migration);
            }
        }


        $this->migrator->run($path);
    }

    /**
     * Migrate package.
     *
     * @param  string  $name
     *
     * @return void
     */
    public function package($name)
    {
        if (starts_with($name, 'src')) {
            $name = str_replace('src/', '', $name);
        }
        $basePath   = rtrim($this->app->make('path.base'), '/');
        $vendorPath = "{$basePath}/src";
        $paths      = [
            "{$vendorPath}/{$name}/resources/database/migrations/",
            "{$vendorPath}/{$name}/database/migrations/",
            "{$vendorPath}/{$name}/migrations/",
        ];
        foreach ($paths as $path) {
            if ($this->app->make('files')->isDirectory($path)) {
                $this->run($path);
            }
        }
        $seeds = [
            "{$vendorPath}/{$name}/resources/database/seeds/",
            "{$vendorPath}/{$name}/database/seeds/",
            "{$vendorPath}/{$name}/seeds/",
        ];
        $this->seed($name, $seeds);
    }

    /**
     * resolve paths of migrations & seeds
     * 
     * @param String $name
     * @param String $directory
     * @return array
     */
    protected function getPaths($name, $directory = 'migrations')
    {
        $package = $this->manager->getAvailableExtensions()->findByName($name);

        if ($package === null) {
            return [];
        }

        $basePath = $package->getPath();

        $paths = [
            "{$basePath}/resources/database/{$directory}/",
            "{$basePath}/resources/{$directory}/",
            "{$basePath}/src/{$directory}/",
        ];

        return $paths;
    }

    /**
     * Migrate extension.
     * 
     * @param  string  $name
     * @return void
     */
    public function extension($name)
    {
        $paths = $this->getPaths($name);
        $files = $this->app->make('files');
        foreach ($paths as $path) {
            if ($files->isDirectory($path)) {
                $this->run($path);
            }
        }
        $this->seed($name);
    }

    /**
     * run seeds from all files in seeds directory
     * 
     * @param type $name
     */
    public function seed($name, $paths = null)
    {
        $directories = is_null($paths) ? $this->getPaths($name, 'seeds') : $paths;
        $files       = $this->app->make('files');


        foreach ($directories as $path) {


            if ($files->isDirectory($path)) {
                $allFiles = $files->allFiles($path);


                foreach ($allFiles as $file) {

                    $class = $this->prepareSeedClass($file);
                    if (!is_null($class)) {
                        $this->seeder->call($this->prepareSeedClass($file));
                    }
                }
            }
        }
    }

    /**
     * get uninstall pathes
     * 
     * @param String $name
     * @param String $directory
     * @return array
     */
    protected function uninstallPathes($name, $directory = 'migrations')
    {
        $package = $this->manager->getAvailableExtensions()->findByName($name);

        if ($package === null) {
            return [];
        }

        $basePath = $package->getPath();

        return [
            "{$basePath}/resources/database/{$directory}/",
            "{$basePath}/resources/{$directory}/",
            "{$basePath}/src/{$directory}/",
        ];
    }

    /**
     * prepare seed classname
     * 
     * @param \Symfony\Component\Finder\SplFileInfo $file
     * @return String
     */
    protected function prepareSeedClass($file)
    {
        $extension = $file->getExtension();
        if ($extension !== 'php') {
            return null;
        }
        return '\\' . str_replace('.' . $extension, '', $file->getFilename());
    }

    /**
     * 
     * @param type $name
     */
    public function unseed($name)
    {
        $paths = $this->uninstallPathes($name, 'seeds');
        $files = $this->app->make('files');
        foreach ($paths as $path) {
            if (!$files->isDirectory($path)) {
                continue;
            }
            $allFiles = $files->allFiles($path);
            foreach ($allFiles as $file) {
                $className = $this->prepareSeedClass($file);
                if (!is_null($className)) {
                    $seed = new $className;
                    $seed->down();
                }
            }
        }
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string  $file
     * @return object
     */
    public function resolveClassname($file)
    {

        $splFile = new SplFileInfo($file);
        $parts   = explode('_', $splFile->getFilename());
        $strings = [];
        foreach ($parts as $part) {
            if (is_numeric($part)) {
                continue;
            }
            array_push($strings, $part);
        }
        $class = Str::studly(implode('_', $strings));
        return new $class;
    }

    /**
     * uninstalling component
     * @param type $name
     */
    public function uninstall($name)
    {
        $this->unseed($name);
        $paths = $this->uninstallPathes($name);
        foreach ($paths as $path) {
            if (!$this->app->make('files')->isDirectory($path)) {
                continue;
            }
            $files = $this->migrator->getMigrationFiles($path);
            foreach ($files as $file) {
                $migration           = $this->resolveClassname(str_replace('.php', '', $file));
                $migrator            = new stdClass();
                $migrator->migration = $file;
                $this->migrator->getRepository()->delete($migrator);
                if (method_exists($migration, 'down')) {
                    $migration->down();
                }
            }
        }
    }

    /**
     * Migrate Antares.
     *
     * @return void
     */
    public function foundation()
    {
        $this->package('core/src/components/memory');
        $this->package('core/src/components/auth');
        $this->package('core/src/utils/form');
        $this->package('core/src/ui/components/templates');
    }

}

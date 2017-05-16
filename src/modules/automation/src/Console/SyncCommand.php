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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\Console;

use Symfony\Component\Finder\Finder as SymfonyFinder;
use Antares\Automation\Model\JobsCategory;
use Antares\Extension\FilesystemFinder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Antares\Automation\Model\Jobs;
use Antares\View\Console\Command;
use Antares\Support\Str;
use ReflectionClass;
use Exception;

class SyncCommand extends Command
{

    /**
     * components container
     *
     * @var type 
     */
    protected $components = [];

    /**
     * Filesystem instance
     *
     * @var Filesystem 
     */
    protected $filesystem;

    /**
     * Manager instance
     *
     * @var FilesystemFinder
     */
    protected $finder;

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Sync Automation Job';

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
    protected $name = 'automation:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize automation jobs.';

    /**
     * Ignored commands container
     *
     * @var array 
     */
    protected $ignored = [];

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->filesystem = app(Filesystem::class);
        $this->finder     = app(FilesystemFinder::class);
        $this->ignored    = config('antares/automation::ignored');
    }

    /**
     * fill components container
     * 
     * @return SyncCommand
     */
    protected function setComponents()
    {
        $components = app('Antares\Model\Component')->all();
        foreach ($components as $component) {
            $this->components[$component->name] = $component->id;
        }
        return $this;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->setComponents();
        DB::beginTransaction();
        try {
            $jobs  = $this->scan();
            $items = [];
            foreach ($jobs as $componentId => $commands) {
                if (empty($commands)) {
                    continue;
                }
                $items = array_merge($items, $this->saveJobs($componentId, $commands));
            }
            Jobs::query()->whereNotIn('name', $items)->delete();
        } catch (Exception $ex) {
            DB::rollback();
            Log::error($ex);
            $this->error('Sync error: ' . $ex->getMessage());
        }
        DB::commit();
        $this->line('Sync completed.');
    }

    /**
     * Saves jobs as commands
     * 
     * @param mixed $componentId
     * @param array $commands
     */
    protected function saveJobs($componentId, $commands)
    {
        $return = [];
        foreach ($commands as $command) {
            $instance   = app($command);
            $categoryId = $this->resolveCategoryId($instance->getCategory());
            $name       = $instance->getName();
            if (in_array($name, $this->ignored)) {
                $this->info(sprintf('Command %s is ignored. Continue...', $name));
                continue;
            }
            array_push($return, $name);
            $model = Jobs::query()->firstOrNew([
                'component_id' => $componentId,
                'category_id'  => $categoryId,
                'name'         => $name
            ]);
            $this->value($model, $instance);
            $model->save();
        }
        return $return;
    }

    /**
     * Model value setter
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param mixed $command
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function value($model, $command)
    {
        return $model->value = [
            'component_id' => $model->component_id,
            'category_id'  => $model->category_id,
            'standalone'   => $command->isStandalone(),
            'description'  => $command->getDescription(),
            'title'        => $command->getTitle(),
            'cron'         => $command->getCron(),
            'launch'       => $command->getLaunchTime(),
            'launchTimes'  => $command->getAvilableLanuchTimes(),
            'classname'    => get_class($command)
        ];
    }

    /**
     * Resolving category id by category name
     * 
     * @param String $name
     * @return mixed
     */
    protected function resolveCategoryId($name)
    {
        $category = JobsCategory::firstOrNew(['name' => $name]);
        if ($category->exists) {
            return $category->id;
        }
        $category->title = Str::humanize($category->name);
        $category->save();
        return $category->id;
    }

    /**
     * Scan application to find jobs
     * 
     * @return array
     */
    protected function scan()
    {
        $extensions = app('antares.extension')->getAvailableExtensions();

        $commands = $this->findJobs(base_path('src/core'));

        foreach ($extensions as $extension) {
            if (!$extension->isActivated()) {
                continue;
            }
            $commands += $this->findJobs($extension->getPath(), ['testbench', 'tests', 'testing']);
        }

        if (empty($commands)) {
            $this->line('No jobs found.');
            return [];
        }
        return $commands;
    }

    /**
     * find component id by manifest file
     * 
     * @param array $directory
     * @return mixed
     */
    protected function findComponentId($directory)
    {

        $componentId  = null;
        $composerPath = $directory . DIRECTORY_SEPARATOR . 'composer.json';

        if (!$this->filesystem->exists($composerPath)) {
            $componentId = $this->components['core'];
        } else {
            $name        = json_decode($this->filesystem->get($composerPath))->name;
            $componentId = $this->components[str_replace('antaresproject/', '', $name)];
        }
        return $componentId;
    }

    /**
     * finds job instances in directory
     * 
     * @param String $directory
     * @param array $exclude
     * @return String
     */
    protected function findJobs($directory, array $exclude = [])
    {
        $componentId = $this->findComponentId($directory);
        $commands    = [];
        if (!is_dir($directory)) {
            return $commands;
        }
        $files = iterator_to_array(SymfonyFinder::create()->files()->ignoreDotFiles(true)->in($directory)->exclude('testbench')->exclude('testing')->exclude('tests'), false);
        foreach ($files as $file) {
            $extension = $file->getExtension();
            if ($extension != 'php') {
                continue;
            }
            $namespace = $this->readNamespace($file->getContents());
            if (is_null($namespace)) {
                continue;
            }

            try {
                $className = $namespace . '\\' . str_replace('.' . $extension, '', $file->getBasename());
                if (!class_exists($className)) {
                    continue;
                }
                $reflectionClass = new ReflectionClass($className);
                $parent          = $reflectionClass->getParentClass();


                if (is_bool($parent)) {
                    continue;
                }
                if ($parent->getName() !== "Antares\View\Console\Command") {
                    continue;
                }

                $commands[$componentId][] = $className;
            } catch (Exception $ex) {
                continue;
            }
        }
        return $commands;
    }

    /**
     * read namespace from file content
     * 
     * @param String $src
     * @return String|null
     */
    protected function readNamespace($src)
    {
        $tokens       = token_get_all($src);
        $count        = count($tokens);
        $i            = 0;
        $namespace    = '';
        $namespace_ok = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Found namespace declaration
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace    = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }
        if (!$namespace_ok) {
            return null;
        } else {
            return $namespace;
        }
    }

}

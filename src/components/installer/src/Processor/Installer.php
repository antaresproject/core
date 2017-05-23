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
 * @version    0.9.2
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Installation\Processor;

use Antares\Extension\Repositories\ComponentsRepository;
use Antares\Contracts\Installation\Installation;
use Antares\Contracts\Installation\Requirement;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
use Antares\Support\Facades\Config;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Antares\Support\Facades\Form;
use Illuminate\Cache\FileStore;
use ReflectionException;
use Antares\Model\User;
use Exception;

class Installer
{

    /**
     * Installer instance.
     *
     * @var Installation
     */
    protected $installer;

    /**
     * Requirement instance.
     *
     * @var Requirement
     */
    protected $requirement;

    /**
     * Create a new processor instance.
     *
     * @param Installation $installer
     * @param Requirement $requirement
     */
    public function __construct(Installation $installer, Requirement $requirement)
    {
        $this->installer   = $installer;
        $this->requirement = $requirement;

        $this->installer->bootInstallerFiles();
    }

    /**
     * Start an installation and check for requirement.
     *
     * @param  object  $listener
     *
     * @return mixed
     */
    public function index($listener)
    {
        app('antares.memory')->forgetCache();

        $requirement = $this->requirement;
        $installable = $requirement->check();
        list($database, $auth, $authentication) = $this->getRunningConfiguration();

        (true === $authentication) || $installable = false;

        $data = [
            'database'       => $database,
            'auth'           => $auth,
            'authentication' => $authentication,
            'installable'    => $installable,
            'checklist'      => $requirement->getChecklist(),
        ];
        $this->clearStorage();
        $this->clearCache();
        return $listener->indexSucceed($data);
    }

    /**
     * Clearing storage files before installation
     */
    protected function clearStorage()
    {
        $filesystem = File::getFacadeRoot();
        $finder     = new Finder();
        $paths      = config('antares/installer::storage_path');
        $finder     = $finder->files()->ignoreVCS(true);
        foreach ($paths as $path) {
            $current = storage_path($path);
            if (!is_dir($current)) {
                continue;
            }
            $finder = $finder->in($current);
        }
        $finder->exclude('.gitignore');
        foreach ($finder as $element) {
            $filesystem->delete($element);
        }
        try {
            $directories = $finder->directories();
            foreach ($directories as $dir) {
                $files = $filesystem->allFiles($dir->getPath(), true);
                if (empty($files)) {
                    $filesystem->deleteDirectory($dir->getPath());
                }
            }
        } catch (Exception $e) {
            Log::emergency($e);
        }
    }

    /**
     * clear global cache
     * 
     * @return boolean
     */
    protected function clearCache()
    {
        try {
            $cache = app('cache');
            $store = $cache->store()->getStore();
            if ($store instanceof FileStore) {
                $directory = $store->getDirectory();
                $store->getFilesystem()->cleanDirectory($directory);
                return true;
            }
        } catch (Exception $e) {
            Log::emergency($e);
            return false;
        }
    }

    /**
     * Run migration and prepare the database.
     *
     * @param  object  $listener
     *
     * @return mixed
     */
    public function prepare($listener)
    {
        $this->clearCache();
        $this->installer->migrate();
        return $listener->prepareSucceed();
    }

    /**
     * Display initial user and site configuration page.
     *
     * @param  object  $listener
     *
     * @return mixed
     */
    public function create($listener)
    {
        return $listener->createSucceed(['siteName' => 'Antares',]);
    }

    /**
     * Store/save administator information and site configuration.
     *
     * @param  object  $listener
     * @param  array   $input
     *
     * @return mixed
     */
    public function store($listener, array $input)
    {

        if ($this->installer->createAdmin($input)) {
            return $listener->storeSucceed();
        }

        return $listener->storeFailed();
    }

    /**
     * Complete the installation.
     *
     * @param  object  $listener
     *
     * @return mixed
     */
    public function done($listener)
    {
        return $listener->doneSucceed();
    }

    /**
     * Get running configuration.
     *
     * @return array
     */
    protected function getRunningConfiguration()
    {
        $driver   = Config::get('database.default', 'mysql');
        $database = Config::get("database.connections.{$driver}", []);
        $auth     = Config::get('auth');

        if (isset($database['password']) && ($password = strlen($database['password']))) {
            $database['password'] = str_repeat('*', $password);
        }

        $authentication = $this->isAuthenticationInstallable($auth);

        return [$database, $auth, $authentication];
    }

    /**
     * Is authentication installable.
     *
     * @param  array    $auth
     *
     * @return bool
     */
    protected function isAuthenticationInstallable($auth)
    {
        try {
            $eloquent = App::make($auth['providers']['users']['model']);
            return ($auth['providers']['users']['driver'] === 'eloquent' && $eloquent instanceof User);
        } catch (ReflectionException $e) {
            Log::emergency($e);
            return false;
        }
    }

    /**
     * @return ComponentsRepository
     */
    private function getComponentsRepository()
    {
        return app()->make(ComponentsRepository::class);
    }

    /**
     * Shows components form
     *
     * @param object $listener
     * @return mixed
     */
    public function components($listener)
    {
        $form = Form::of('components', function ($form) {
                    $form->layout('antares/installer::partials._components_form');
                    $attributes = [
                        'url'    => handles("antares::install/components/store"),
                        'method' => 'POST'
                    ];
                    $form->attributes($attributes);
                    $form->name('Components');
                    $form->fieldset(function ($fieldset) {

                        $fieldset->legend('Available optional components');
                        $optional   = array_keys($this->getComponentsRepository()->getOptional());
                        $extensions = app('antares.extension')->getAvailableExtensions();
                        foreach ($extensions as $extension) {
                            $name = $extension->getVendorName() . '/' . $extension->getPackageName();
                            if (!in_array($name, $optional)) {
                                continue;
                            }
                            $package = $extension->getPackage();
                            $fieldset->control('input:checkbox', 'optional[]')
                                    ->label($extension->getFriendlyName() . ' (' . $package->getVersion() . ')')
                                    ->value($name)
                                    ->help($package->getDescription() . ', ' . implode(', ', array_flatten($package->getAuthors())))
                                    ->checked(true);
                        }
                    });
                });
        return $listener->componentsSucceed(['form' => $form]);
    }

    /**
     * launch components/modules installation.
     *
     * @param  object  $listener
     * @param  array   $selected
     *
     * @return mixed
     */
    public function storeComponents($listener, array $selected)
    {
        try {
            $required   = array_keys($this->getComponentsRepository()->getRequired());
            $extensions = array_merge($required, $selected);
            $this->installer->runComponentsInstallation($extensions);
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->doneFailed();
        }
        return $listener->showInstallProgress();
    }

}

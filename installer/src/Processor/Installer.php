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

namespace Antares\Installation\Processor;

use Antares\Contracts\Installation\Installation;
use Antares\Contracts\Installation\Requirement;
use Antares\Installation\Repository\Components;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
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
     * components repository instance
     *
     * @var Components
     */
    protected $components;

    /**
     * Create a new processor instance.
     *
     * @param Installation $installer
     * @param Requirement $requirement
     * @param Components $components
     */
    public function __construct(Installation $installer, Requirement $requirement, Components $components)
    {
        $this->installer   = $installer;
        $this->requirement = $requirement;
        $this->installer->bootInstallerFiles();
        $this->components  = $components;
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
            
        }
        return;
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
        if (!$this->installer->createAdmin($input)) {
            return $listener->storeFailed();
        }
        return $listener->storeSucceed();
    }

    /**
     * launch components/modules installation.
     *
     * @param  object  $listener
     * @param  array   $input
     *
     * @return mixed
     */
    public function storeComponents($listener, array $input)
    {
        try {
            $this->components->store($input);
            app('antares.memory')->make('component')->finish();
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->doneFailed();
        }
        return $this->done($listener);
    }

    /**
     * shows components form
     * 
     * @param object $listener
     * @return mixed
     */
    public function components($listener)
    {
        $form = Form::of('components', function ($form) {
                    $attributes = [
                        'url'    => handles("antares::install/components/store"),
                        'method' => 'POST'
                    ];
                    $form->attributes($attributes);
                    $list       = $this->getComponentsList();

                    $form->name('Components list');
                    $form->layout('antares/installer::partials._components_form');
                    $form->fieldset(function ($fieldset) use($list) {

                        $fieldset->legend('Required components');
                        $required = array_get($list, 'required', []);
                        foreach ($required as $name => $data) {
                            $fieldset->control('input:checkbox', 'required[]')
                                    ->label(array_get($data, 'full_name'))
                                    ->value($name)
                                    ->help(implode(', ', array_only($data, ['description', 'author', 'version'])))
                                    ->checked()
                                    ->attributes(['disabled' => 'disabled', 'readonly' => 'readonly']);
                            ;
                        }
                    });
                    $form->fieldset(function ($fieldset) use($list) {
                        $fieldset->legend('Available optional components');
                        $available = array_get($list, 'list', []);
                        $optional  = array_get($list, 'optional', []);

                        foreach ($available as $name => $data) {
                            $checked = in_array($name, $optional);
                            $fieldset->control('input:checkbox', 'extension[]')
                                    ->label(array_get($data, 'full_name'))
                                    ->value($name)
                                    ->help(implode(', ', array_only($data, ['description', 'author', 'version'])))
                                    ->checked($checked);
                        }

                        $fieldset->control('button', 'button')
                                ->attributes(['type' => 'submit', 'class' => 'btn btn--md btn--primary mdl-button mdl-js-button'])
                                ->value(trans('antares/foundation::label.next') . ' <i class="pl8 zmdi zmdi-long-arrow-right"></i>');
                    });
                });
        return $listener->componentsSucceed(['form' => $form]);
    }

    /**
     * Gets list of available components
     * 
     * @return array
     */
    protected function getComponentsList()
    {
        $config     = config('installer.required', []);
        $optional   = config('installer.optional', []);
        $list       = app('antares.extension.finder')->detect();
        $required   = [];
        $components = [];
        $list->each(function($element, $key) use($config, &$list, &$required, &$components) {
            if (in_array($key, $config)) {
                $required = array_add($required, $element['name'], $element);
                $list->forget($key);
            } else {
                $components[$key] = $element;
            }
        });
        return [
            'optional' => $optional,
            'list'     => $components,
            'required' => $required
        ];
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
        app('antares.extension')->detect();
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

}

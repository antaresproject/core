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


namespace Antares\Extension\Processor;

use Antares\Contracts\Extension\Listener\Migrator as Listener;
use Antares\Contracts\Extension\Factory;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Antares\Widgets\Model\Widgets;
use Illuminate\Support\Fluent;
use Antares\Model\Component;
use Exception;

class Uninstaller extends Processor
{

    /**
     * uninstall an extension.
     * @param  \Antares\Contracts\Extension\Listener\Migrator  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     * @return mixed
     */
    public function uninstall(Listener $listener, Fluent $extension, $useTransaction = true)
    {
        $name = $extension->get('name');
        if ($useTransaction) {
            DB::beginTransaction();
        }
        $exception = false;
        try {
            $name      = $extension->get('name');
            $names     = explode('/', $name);
            $component = Component::query()->where('name', '=', end($names))->get()->first();
            if (is_null($component)) {
                DB::rollback();
                return true;
            }
            $component->actions()->delete();
            $component->status = 0;
            $component->save();

            $this->clearWidgets($component);

            $this->execute($listener, 'migration', $extension, $this->getUninstallClosure());

            $memory  = app('antares.memory');
            $current = $memory->get('extensions.active', []);
            $active  = [];

            foreach ($current as $extension => $config) {
                if ($extension !== $name) {
                    $active[$extension] = $config;
                }
            }
            $memory->put('extensions.active', $active);
            $memory->finish();
            $memory->make('component')->getHandler()->forgetCache();
        } catch (Exception $ex) {
            Log::emergency($ex);
            $exception = $ex;
        }
        if ($useTransaction) {
            if ($exception == false) {
                DB::commit();
            } else {
                DB::rollback();
            }
        }
        return $exception === false;
    }

    /**
     * Get migration closure.
     * @return callable
     */
    protected function getUninstallClosure()
    {
        return function (Factory $factory, $name) {
            $factory->uninstall($name);
        };
    }

    /**
     * clear all widgets instances
     * 
     * @param Component $component
     * @return void
     */
    protected function clearWidgets($component)
    {
        $extensionFinder = app('antares.extension.finder');
        $path            = $extensionFinder->resolveExtensionPath($component->path);
        $directories     = with(new Finder())->directories()->in($path);

        foreach ($directories as $directory) {
            if ($directory->getBasename() !== 'Widgets') {
                continue;
            }
            $files = with(new Finder())->files()->in($directory->getRealpath());
            foreach ($files as $file) {
                $name  = $this->getWidgetName($file);
                $model = Widgets::where('name', $name);
                if (!is_null($model)) {
                    $model->delete();
                }
            }
        }
        return;
    }

    /**
     * widget instance getter
     * 
     * @param \SplFileInfo $file
     * @return String|null
     */
    protected function getWidgetName($file)
    {
        $namespace = app('antares.widgets.finder')->resolveWidgetNamespace(file_get_contents($file->getRealpath()));
        $classname = $namespace . '\\' . str_replace([$file->getExtension(), '.'], '', $file->getBasename());
        return !class_exists($classname) ? : app($classname)->getName();
    }

}

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


namespace Antares\Extension\Traits;

use Illuminate\Support\Facades\DB;
use Antares\Model\Component;
use Exception;

trait OperationTrait
{

    /**
     * Activate an extension.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function activate($name)
    {
        return $this->activating($name);
    }

    /**
     * Activating an extension.
     *
     * @param  string  $name
     *
     * @return bool
     */
    protected function activating($name)
    {
        $memory    = $this->memory;
        $available = $memory->get('extensions.available', []);
        $active    = $memory->get('extensions.active', []);
        if (!isset($available[$name])) {
            return;
        }

        $active[$name] = $available[$name];
        DB::beginTransaction();
        try {
            $this->extensions[$name] = $active[$name];
            $this->publish($name);
            $this->dispatcher->register($name, $active[$name]);
            $this->app->make('events')->fire("antares.activating: {$name}", [$name]);
            $memory->put('extensions.active', $active);
            $memory->finish();
            app('antares.memory')->make('component')->finish();

            $memory->getHandler()->forgetCache();
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception($e);
        }
        DB::commit();
        return true;
    }

    /**
     * changing component status 
     * 
     * @param String $name
     * @return boolean
     */
    public function up($name)
    {
        try {
            $memory    = $this->memory;
            $available = $memory->get('extensions.available', []);
            $active    = $memory->get('extensions.active', []);
            if (!isset($available[$name])) {
                return;
            }
            $active[$name] = $available[$name];
            $model         = Component::query()->where('name', '=', $active[$name]['name'])->firstOrFail();
            $model->status = 1;
            $model->save();
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    /**
     * Check whether an extension is active.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function activated($name)
    {
        return is_array($this->memory->get("extensions.active.{$name}"));
    }

    /**
     * Check whether an extension is available.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function available($name)
    {
        return is_array($this->memory->get("extensions.available.{$name}"));
    }

    /**
     * Deactivate an extension.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function deactivate($name)
    {
        $deactivated = false;
        $memory      = $this->memory;
        $current     = $memory->get('extensions.active', []);
        $active      = [];

        foreach ($current as $extension => $config) {

            if ($extension === $name) {
                $deactivated = true;
            } else {
                $active[$extension] = $config;
            }
        }
        $model         = Component::query()->where('name', '=', $current[$name]['name'])->firstOrFail();
        $model->status = 0;
        $model->save();
        if ($deactivated) {
            $memory->put('extensions.active', $active);
            $this->app->make('events')->fire("antares.deactivating: {$name}", [$name]);
        }
        return $deactivated;
    }

    /**
     * Refresh extension configuration.
     *
     * @param  string  $name
     *
     * @return array|null
     */
    public function refresh($name)
    {
        $memory    = $this->memory;
        $available = $memory->get('extensions.available', []);
        $active    = $memory->get('extensions.active', []);



        if (!isset($available[$name])) {
            return;
        }

        $active[$name] = $available[$name];
        $memory->put('extensions.active', $active);


        return $active;
    }

    /**
     * Reset extension.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function reset($name)
    {
        $memory  = $this->memory;
        $default = $memory->get("extensions.available.{$name}", []);

        $memory->put("extensions.active.{$name}", $default);

        if ($memory->has("extension_{$name}")) {
            $memory->put("extension_{$name}", []);
        }

        return true;
    }

    /**
     * Check if extension is started.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function started($name)
    {
        return $this->extensions->has($name);
    }

}

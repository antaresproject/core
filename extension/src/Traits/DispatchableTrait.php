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

use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Closure;

trait DispatchableTrait
{

    /**
     * Booted indicator.
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * Debugger (safe mode) instance.
     *
     * @var \Antares\Contracts\Extension\SafeMode
     */
    protected $mode;

    /**
     * Boot active extensions.
     *
     * @return $this
     */
    public function boot()
    {

        if (!($this->booted() || $this->mode->check())) {

            $this->booted = true;

            $this->registerActiveExtensions();

            $this->dispatcher->boot();

            $this->events->fire('antares.extension: booted');
        }

        return $this;
    }

    /**
     * Boot active extensions.
     *
     * @return $this
     */
    public function booted()
    {
        return $this->booted;
    }

    /**
     * Shutdown all extensions.
     *
     * @return $this
     */
    public function finish()
    {
        foreach ($this->extensions as $name => $options) {
            $this->dispatcher->finish($name, $options);
        }

        $this->extensions = new Collection();

        return $this;
    }

    /**
     * Register all active extension to dispatcher.
     *
     * @return void
     */
    protected function registerActiveExtensions()
    {
        $available = $this->memory->get('extensions.available', []);
        $active    = $this->memory->get('extensions.active', []);

        foreach ($active as $name => $options) {
            if (isset($available[$name])) {
                $config = array_merge(
                        (array) Arr::get($available, "{$name}.config"), (array) Arr::get($options, 'config')
                );

                Arr::set($options, 'config', $config);
                $this->extensions[$name] = $options;
                $this->dispatcher->register($name, $options);
            }
        }
    }

    /**
     * Create an event listener or execute it directly.
     *
     * @param  \Closure|null  $callback
     *
     * @return void
     */
    public function after(Closure $callback = null)
    {
        if ($this->booted()) {
            $this->app->call($callback);
        }

        $this->events->listen('antares.extension: booted', $callback);
    }

}

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
 namespace Antares\Support\Traits;

use Illuminate\Contracts\Events\Dispatcher;

trait ObservableTrait
{
    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected static $dispatcher;

    /**
     * Register an observer.
     *
     * @param  object  $class
     *
     * @return void
     */
    public static function observe($class)
    {
        $instance = new static();

        $className = get_class($class);

        foreach ($instance->getObservableEvents() as $event) {
            if (method_exists($class, $event)) {
                static::registerObservableEvent($event, "{$className}@{$event}");
            }
        }
    }

    /**
     * Get the observer key.
     *
     * @param  string  $event
     *
     * @return string
     */
    protected function getObservableKey($event)
    {
        return $event;
    }

    /**
     * Get the observable events.
     *
     * @return array
     */
    public function getObservableEvents()
    {
        return [];
    }

    /**
     * Register an event with the dispatcher.
     *
     * @param  string  $event
     * @param  \Closure|string  $callback
     *
     * @return void
     */
    protected static function registerObservableEvent($event, $callback)
    {
        if (! isset(static::$dispatcher)) {
            return ;
        }

        $className = get_called_class();

        $event = with(new static())->getObservableKey($event);

        static::$dispatcher->listen("{$event}: {$className}", $callback);
    }

    /**
     * Fire the given event.
     *
     * @param  string  $event
     * @param  bool    $halt
     *
     * @return mixed
     */
    protected function fireObservableEvent($event, $halt)
    {
        if (! isset(static::$dispatcher)) {
            return true;
        }

        $className = get_class($this);
        $event     = $this->getObservableKey($event);

        $method = $halt ? 'until' : 'fire';

        return static::$dispatcher->$method("{$event}: {$className}", $this);
    }

    /**
     * Remove all of the event listeners for the observers.
     *
     * @return void
     */
    public static function flushEventListeners()
    {
        if (! isset(static::$dispatcher)) {
            return ;
        }

        $instance  = new static();
        $className = get_called_class();

        foreach ($instance->getObservableEvents() as $event) {
            $event = $instance->getObservableKey($event);

            static::$dispatcher->forget("{$event}: {$className}");
        }
    }

    /**
     * Get the event dispatcher instance.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    public static function getEventDispatcher()
    {
        return static::$dispatcher;
    }

    /**
     * Set the event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     *
     * @return void
     */
    public static function setEventDispatcher(Dispatcher $dispatcher)
    {
        static::$dispatcher = $dispatcher;
    }

    /**
     * Unset the event dispatcher instance.
     *
     * @return void
     */
    public static function unsetEventDispatcher()
    {
        static::$dispatcher = null;
    }
}

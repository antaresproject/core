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


namespace Antares\Support\Providers\Traits;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;

trait EventProviderTrait
{

    /**
     * Register the application's event listeners.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     *
     * @return void
     */
    public function registerEventListeners(DispatcherContract $events)
    {
        foreach ($this->listen as $event => $listeners) {
            $defaultPriority = 0;
            if (!is_array($listeners)) {
                $events->listen($event, $listeners);
                continue;
            }
            foreach ($listeners as $listener => $priority) {
                if (is_numeric($listener)) {
                    $listener = $priority;
                    $priority = $defaultPriority;
                }
                if (is_array($listener)) {
                    $priority = is_numeric(current($listener)) ? current($listener) : $defaultPriority;
                    $listener = key($listener);
                }
                $events->listen($event, $listener, $priority);
            }
        }
        foreach ($this->subscribe as $subscriber) {
            $events->subscribe($subscriber);
        }
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }

}

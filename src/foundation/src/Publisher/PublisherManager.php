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


namespace Antares\Foundation\Publisher;

use Exception;
use Illuminate\Support\Manager;
use Antares\Memory\ContainerTrait;
use Illuminate\Support\Facades\Log;

class PublisherManager extends Manager
{

    use ContainerTrait;

    /**
     * Create a new driver instance.
     *
     * @param  string  $driver
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    protected function createDriver($driver)
    {
        return $this->app->make("antares.publisher.{$driver}");
    }

    /**
     * Get the default authentication driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->memory->get('antares.publisher.driver', 'ftp');
    }

    /**
     * Execute the queue.
     *
     * @return bool
     */
    public function execute()
    {
        $messages = $this->app->make('antares.messages');
        $queues   = $this->queued();
        $fails    = [];

        foreach ($queues as $queue) {
            try {
                $this->driver()->upload($queue);

                $messages->add('success', trans('antares/foundation::response.extensions.activate', [
                    'name' => $queue,
                ]));
            } catch (Exception $e) {
                Log::emergency($e);
                $messages->add('error', $e->getMessage());
                $fails[] = $queue;
            }
        }

        $this->memory->put('antares.publisher.queue', $fails);

        return true;
    }

    /**
     * Add a process to be queue.
     *
     * @param  string  $queue
     *
     * @return bool
     */
    public function queue($queue)
    {
        $queue = array_unique(array_merge($this->queued(), (array) $queue));
        $this->memory->put('antares.publisher.queue', $queue);

        return true;
    }

    /**
     * Get a current queue.
     *
     * @return array
     */
    public function queued()
    {
        return $this->memory->get('antares.publisher.queue', []);
    }

}

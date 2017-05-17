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


namespace Antares\Foundation\Processor;

use Illuminate\Session\Store;
use Illuminate\Support\Facades\Log;
use Antares\Contracts\Publisher\ServerException;
use Antares\Foundation\Publisher\PublisherManager;
use Antares\Contracts\Foundation\Command\AssetPublisher as Command;
use Antares\Contracts\Foundation\Listener\AssetPublishing as Listener;

class AssetPublisher extends Processor implements Command
{

    /**
     * The publisher manager implementation.
     *
     * @var \Antares\Foundation\Publisher\PublisherManager
     */
    protected $publisher;

    /**
     * The session store implementation.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Create a new instance of Asset Publisher.
     *
     * @param \Antares\Foundation\Publisher\PublisherManager $publisher
     * @param \Illuminate\Session\Store  $session
     */
    public function __construct(PublisherManager $publisher, Store $session)
    {
        $this->publisher = $publisher;
        $this->session   = $session;
    }

    /**
     * Run publishing if possible.
     *
     * @param  \Antares\Contracts\Foundation\Listener\AssetPublishing  $listener
     *
     * @return mixed
     */
    public function executeAndRedirect(Listener $listener)
    {
        $this->publisher->connected() && $this->publisher->execute();

        return $listener->redirectToCurrentPublisher();
    }

    /**
     * Publish process.
     *
     * @param  \Antares\Contracts\Foundation\Listener\AssetPublishing  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function publish(Listener $listener, array $input)
    {
        $queues = $this->publisher->queued();

        try {
            $this->publisher->connect($input);
        } catch (ServerException $e) {
            Log::emergency($e);
            $this->session->forget('antares.ftp');

            return $listener->publishingHasFailed(['error' => $e->getMessage()]);
        }

        $this->session->put('antares.ftp', $input);

        if ($this->publisher->connected() && !empty($queues)) {
            $this->publisher->execute();
        }

        return $listener->publishingHasSucceed();
    }

}

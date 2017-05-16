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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Strategy\Sandbox;

use Antares\Updater\Contracts\SessionBroadcaster as SessionBroadcasterContract;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

class SessionBroadcaster extends AbstractStrategy implements SessionBroadcasterContract
{

    /**
     * session adapter instance
     *
     * @var \Antares\Updater\Contracts\StorageAdapter
     */
    protected $adapter;

    /**
     * constructing
     * 
     * @param Repository $config
     * @param Application $app
     */
    public function __construct(Repository $config, Application $app)
    {
        $model         = $config->get('antares/updater::sandbox.session.adapters.default.model');
        $this->adapter = $app->make($model);
    }

    /**
     * 
     * @return type
     */
    public function broadcast()
    {
        return $this->adapter->replicate();
    }

}

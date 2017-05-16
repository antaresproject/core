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






namespace Antares\Updater;

use Illuminate\Contracts\Foundation\Application;
use Antares\Updater\Contracts\Factory as FactoryContract;

class Factory implements FactoryContract
{

    /**
     * application container
     *
     * @var Application
     */
    protected $app;

    /**
     * default service adapter
     *
     * @var array 
     */
    protected $config;

    /**
     * constructing
     * 
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app    = $app;
        $this->config = $app->make('config')->get('antares/updater::service.adapters');
    }

    /**
     * get instance of system info adapter
     * 
     * @return Adapter\AbstractAdapter
     */
    public function getAdapter()
    {
        return $this->app->make(array_get($this->config, 'default.model'));
    }

}

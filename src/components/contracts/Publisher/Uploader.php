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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Contracts\Publisher;

interface Uploader
{
    /**
     * Get service connection instance.
     *
     * @return object
     */
    public function getConnection();

    /**
     * Get service connection instance.
     *
     * @param  object  $client
     *
     * @return void
     */
    public function setConnection($client);

    /**
     * Connect to the service.
     *
     * @param  array  $config
     *
     * @return void
     */
    public function connect($config = []);

    /**
     * Upload the file.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function upload($name);

    /**
     * Verify that the driver is connected to a service.
     *
     * @return bool
     */
    public function connected();
}

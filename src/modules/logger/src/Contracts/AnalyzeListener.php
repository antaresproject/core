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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Contracts;

interface AnalyzeListener
{

    /**
     * get infromation about list of urls for analyzer runner
     */
    public function index();

    /**
     * read server environment
     */
    public function server();

    /**
     * read modules list 
     */
    public function modules();

    /**
     * get system version
     */
    public function version();

    /**
     * report database tables 
     */
    public function database();

    /**
     * get informations about logs
     */
    public function logs();
}

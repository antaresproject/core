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

namespace Antares\Logger\Utilities;

use Arcanedev\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Arcanedev\LogViewer\Contracts\Utilities\LogLevels as LogLevelsContract;
use Arcanedev\LogViewer\LogViewer as SupportLogViewer;
use Antares\Logger\Contracts\FactoryInterface;

class LogViewer extends SupportLogViewer
{

    /**
     * Create a new instance.
     *
     * @param  FactoryInterface     $factory
     * @param  FilesystemInterface  $filesystem
     * @param  LogLevelsInterface   $levels
     */
    public function __construct(FactoryInterface $factory, FilesystemContract $filesystem, LogLevelsContract $levels)
    {
        $this->factory    = $factory;
        $this->filesystem = $filesystem;
        $this->levels     = $levels;
    }

    /**
     * Get a log.
     *
     * @param  string  $date
     *
     * @return \Antares\Logger\Entities\Log
     */
    public function get($date)
    {
        return $this->factory->log($date);
    }

}

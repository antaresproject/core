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



namespace Antares\Logger;

use Antares\Logger\Collection\LogsCollection;
use Antares\Logger\Contracts\FactoryInterface;
use Arcanedev\LogViewer\Utilities\Factory as SupportFactory;

class LoggerFactory extends SupportFactory implements FactoryInterface
{

    /**
     * Get all logs.
     *
     * @return \Arcanedev\LogViewer\Entities\LogCollection
     */
    public function logs()
    {
        return LogsCollection::make()->setFilesystem($this->filesystem);
    }

    /**
     * Get a log by date.
     *
     * @param  string  $date
     *
     * @return \Arcanedev\LogViewer\Entities\Log
     */
    public function log($date)
    {
        return $this->logs()->log($date);
    }

}

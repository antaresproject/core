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

namespace Antares\Logger\Collection;

use Arcanedev\LogViewer\Contracts\Utilities\Filesystem as FilesystemContract;
use Arcanedev\LogViewer\Entities\LogCollection as SupportLogsCollection;
use Antares\Logger\Entities\Log;

class LogsCollection extends SupportLogsCollection
{

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * Constructor
     *
     * @param  array  $items
     */
    public function __construct($items = [])
    {
        $this->setFilesystem(app(FilesystemContract::class));

        if (empty($items)) {
            $this->load();
        }
    }

    /**
     * Set the filesystem instance.
     *
     * @param  FilesystemContract  $filesystem
     *
     * @return self
     */
    public function setFilesystem(FilesystemContract $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Get a log.
     *
     * @param  string     $date
     * @param  mixed|null $default
     *
     * @return Log
     */
    public function get($date, $default = null)
    {
        if (!$this->has($date)) {
            return false;
        }
        return parent::get($date, $default);
    }

    /**
     * Load all logs.
     *
     * @return self
     */
    private function load()
    {
        foreach ($this->filesystem->dates(true) as $date => $path) {
            $raw = $this->filesystem->read($date);
            $this->put($date, Log::make($date, $path, $raw));
        }


        return $this;
    }

    /**
     * Put an item in the collection by key.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return $this
     */
    public function put($key, $value)
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

}

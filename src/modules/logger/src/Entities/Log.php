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



namespace Antares\Logger\Entities;

use Arcanedev\LogViewer\Entities\Log as SupportLog;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class Log extends SupportLog implements Arrayable, Jsonable, JsonSerializable
{

    /**
     * Get log entries menu.
     *
     * @param  bool|true  $trans
     *
     * @return array
     */
    public function menu($trans = true)
    {
        return app('logger.log-viewer.menu')->make($this, $trans);
    }

    /**
     * Make a log object
     *
     * @param  string  $date
     * @param  string  $path
     * @param  string  $raw
     *
     * @return self
     */
    public static function make($date, $path, $raw)
    {
        return new self($date, $path, $raw);
    }

    /**
     * Gets error log filename
     * 
     * @return String
     */
    public function getFilename()
    {
        return last(explode(DIRECTORY_SEPARATOR, $this->getPath()));
    }

}

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


namespace Antares\Memory\Handlers;

use Antares\Contracts\Memory\Handler as HandlerContract;
use Illuminate\Support\Facades\Session as SessionHandler;
use Illuminate\Filesystem\Filesystem;

class Session implements HandlerContract
{

    private $files = null;
    private $path  = null;

    /**
     * Storage name.
     * @var string
     */
    protected $storage = 'file';

    /**
     * Load empty data for runtime.
     *
     * @return array
     */
    public function initiate()
    {
        $this->files = app('filesystem');
        $this->setPath(storage_path() . '/runtime.json');

        return [];
    }

    /**
     * Set the path for the JSON file.
     * @param string $path
     */
    public function setPath($path)
    {

                if (!$this->files->exists($path)) {

            $result = $this->files->put($path, '{}');
            if ($result === false) {
                throw new \InvalidArgumentException("Could not write to $path.");
            }
        }

        if (!$this->files->isWritable($path)) {
            throw new \InvalidArgumentException("$path is not writable.");
        }

        $this->path = $path;
    }

    /**
     * Save empty data to /dev/null.
     * @param  array  $items
     * @return bool
     */
    public function finish(array $items = [])
    {
        return true;
    }

    public function get($name, $default = null)
    {
        $contents = $this->files->get($this->path);
        $data     = json_decode($contents, true);

        if ($data === null) {
            throw new \RuntimeException("Invalid JSON in {$this->path}");
        }

        return $data;
    }

    public function put($key, $value = '')
    {

        $data = [$key => $value];
        if ($data) {
            $contents = json_encode($data);
        } else {
            $contents = '{}';
        }
        $this->files->put($this->path, $contents);
        return $value;
    }

}

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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Extension\Config;

use Antares\Memory\MemoryManager;
use Illuminate\Contracts\Config\Repository as Config;

class Repository
{

    /**
     * Config instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Memory instance.
     *
     * @var \Antares\Memory\MemoryManager
     */
    protected $memory;

    /**
     * Construct a new Config Repository instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @param  \Antares\Memory\MemoryManager  $memory
     */
    public function __construct(Config $config, MemoryManager $memory)
    {
        $this->config = $config;
        $this->memory = $memory;
    }

    /**
     * Map configuration to allow antares to store it in database.
     *
     * @param  string  $name
     * @param  array   $aliases
     *
     * @return bool
     */
    public function map($name, $aliases)
    {
        $memory = $this->memory->make();
        $meta   = $memory->get("extension_{$name}", []);

        foreach ($aliases as $current => $default) {
            isset($meta[$current]) && $this->config->set($default, $meta[$current]);

            $meta[$current] = $this->config->get($default);
        }

        $memory->put("extension_{$name}", $meta);

        return true;
    }

}

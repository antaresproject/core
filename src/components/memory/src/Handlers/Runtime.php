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

use Antares\Memory\Handler;
use Antares\Contracts\Memory\Handler as HandlerContract;

class Runtime extends Handler implements HandlerContract
{
    /**
     * Storage name.
     *
     * @var string
     */
    protected $storage = 'runtime';

    /**
     * Load empty data for runtime.
     *
     * @return array
     */
    public function initiate()
    {
        return [];
    }

    /**
     * Save empty data to /dev/null.
     *
     * @param  array  $items
     *
     * @return bool
     */
    public function finish(array $items = [])
    {
        return true;
    }
}

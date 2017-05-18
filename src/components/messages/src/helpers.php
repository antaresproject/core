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


if (! function_exists('messages')) {
    /**
     * Add a message to the bag.
     *
     * @param  string  $key
     * @param  string  $message
     *
     * @return \Antares\Messages\MessageBag
     */
    function messages($key, $message)
    {
        return app('antares.messages')->add($key, $message);
    }
}

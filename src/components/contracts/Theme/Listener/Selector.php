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
 namespace Antares\Contracts\Theme\Listener;

interface Selector
{
    /**
     * Response when list themes page succeed.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showThemeSelection(array $data);

    /**
     * Response when theme activation succeed.
     *
     * @param  string  $type
     * @param  string  $id
     *
     * @return mixed
     */
    public function themeHasActivated($type, $id);

    /**
     * Response when theme verification failed.
     *
     * @return mixed
     */
    public function themeFailedVerification();
}

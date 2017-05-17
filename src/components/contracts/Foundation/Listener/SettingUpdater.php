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
 namespace Antares\Contracts\Foundation\Listener;

interface SettingUpdater
{
    /**
     * Response when show setting page.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showSettingChanger(array $data);

    /**
     * Response when update setting failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     *
     * @return mixed
     */
    public function settingFailedValidation($errors);

    /**
     * Response when update setting succeed.
     *
     * @return mixed
     */
    public function settingHasUpdated();
}

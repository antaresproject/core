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
 namespace Antares\Contracts\Extension\Listener;

use Illuminate\Support\Fluent;

interface Configure extends Extension
{
    /**
     * Response for extension configuration.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showConfigurationChanger(array $data);

    /**
     * Response when update extension configuration failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     * @param  string  $id
     *
     * @return mixed
     */
    public function updateConfigurationFailedValidation($errors, $id);

    /**
     * Response to extension configuration has succeed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function configurationUpdated(Fluent $extension);
}

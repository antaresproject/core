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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Contracts;

interface FieldCreator
{

    /**
     * Response when create customfield page succeed.
     * @param  array  $data
     * @return mixed
     */
    public function showFieldCreator(array $data);

    /**
     * Response when storing Customfiel failed on validation.
     * @param  \Illuminate\Support\MessageBag|array  $errors
     * @return mixed
     */
    public function createFieldFailedValidation($errors);

    /**
     * Response when storing Customfield failed.
     * @param  array  $errors
     * @return mixed
     */
    public function createFieldFailed(array $errors);

    /**
     * Response when storing brand succeed.
     * @return mixed
     */
    public function fieldCreated();
}

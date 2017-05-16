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

interface FieldUpdater extends Field
{

    /**
     * Response when update customfield page succeed.
     * @param  array  $data
     * @return mixed
     */
    public function showFieldUpdater(array $data);

    /**
     * Response when update Customfiel failed on validation.
     * @param  \Illuminate\Support\MessageBag|array  $errors
     * @return mixed
     */
    public function updateFieldFailedValidation($errors, $id);

    /**
     * Response when update customfield failed.
     * @param  array  $errors
     * @return mixed
     */
    public function updateFieldFailed(array $errors);

    /**
     * Response when update customfield succeed.
     * @return mixed
     */
    public function fieldUpdated();
}

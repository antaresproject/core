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

interface FieldRemover
{

    /**
     * Response when remove customfield failed.
     * @param  array  $errors
     * @return mixed
     */
    public function removeFieldFailed(array $errors);

    /**
     * Response when remove customfield succeed.
     * @return mixed
     */
    public function fieldRemoved();
}

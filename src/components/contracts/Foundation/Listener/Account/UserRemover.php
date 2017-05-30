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
 namespace Antares\Contracts\Foundation\Listener\Account;

interface UserRemover
{
    /**
     * Response when user tried to self delete.
     *
     * @return mixed
     */
    public function selfDeletionFailed();

    /**
     * Response when destroying user failed.
     *
     * @param  array  $errors
     *
     * @return mixed
     */
    public function userDeletionFailed(array $errors);

    /**
     * Response when destroying user succeed.
     *
     * @return mixed
     */
    public function userDeleted();
}

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


return [
    'driver'   => 'eloquent',
    'model'    => 'Antares\Model\User',
    'table'    => 'users',
    'password' => [
        'email'  => 'emails.password',
        'table'  => 'tbl_password_resets',
        'expire' => 60,
    ],
];

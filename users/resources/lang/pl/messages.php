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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


return [
    'register'          => [
        'db-failed' => 'Nie można dodać użytkownika. Skontaktuj się z pomocą.',
    ],
    'fieldsets'         => [
        'user_details' => 'Szczegóły klienta',
    ],
    'status'            => 'Status',
    'statuses'          => [
        'all'      => 'Wszystkie',
        'active'   => 'Aktywni (:count)',
        'archived' => 'Zarchiwizowani (:count)',
    ],
    'created_at'        => 'Utworzono',
    'created_at_filter' => 'Utworzono (:start - :end)',
];

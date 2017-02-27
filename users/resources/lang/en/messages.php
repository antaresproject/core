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
        'db-failed' => 'Unable to register user. Please conttact with support.'
    ],
    'fieldsets'         => [
        'user_details' => 'User details'
    ],
    'status'            => 'Status',
    'statuses'          => [
        'all'      => 'All',
        'active'   => 'Active (:count)',
        'archived' => 'Archived (:count)'
    ],
    'created_at'        => 'Created at',
    'created_at_filter' => 'Created at (:start - :end)',
    'dependable'        => [
        'activate_title'              => 'Activate',
        'deactivate_title'            => 'Deactivate',
        'activate_description'        => 'Activating user :fullname as dependable action.',
        'deactivate_description'      => 'Deactivating user :fullname as dependable action.',
        'status_has_not_been_changed' => 'User status has not been changed.',
        'status_has_been_changed'     => 'User status has been changed.',
        'user_change_status_question' => 'Are you sure to change user status?',
        'mass_actions'                => [
            'change_status_title'       => 'Change status (dependable)',
            'change_status_question'    => 'Are you sure to change status of selected users?',
            'change_status_description' => 'Changing status of selected users...'
        ]
    ]
];

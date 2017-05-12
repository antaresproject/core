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
    'demo'                         => [
        'admin' => [
            'email'    => 'demo.antaresproject@gmail.com',
            'password' => 'demo',
        ],
        'user'  => [
            'email'    => '16dasia97@hotmail.com',
            'password' => 'demo',
        ],
    ],
    'handles'                      => 'antares',
    'routes'                       => [
        'guest'    => 'login',
        'admin'    => 'antares.dashboard',
        'reseller' => 'antares.dashboard',
        'member'   => 'client',
    ],
    'routes-not-allowed'           => [
        'guest'    => 'login',
        'admin'    => 'login',
        'reseller' => 'login',
        'member'   => 'client',
    ],
    'roles'                        => [
        'admin'  => 1,
        'member' => 2,
    ],
    'throttle'                     => [
        /*
          |------------------------------------------------------------------
          | Default Resolver
          |------------------------------------------------------------------
          |
          | The default for handling login throttles, by default we use the
          | default `BasicThrottle` which is identical Laravel offering.
          | However you can disable it by changing to `WithoutThrottle`.
          |
         */
        'resolver'   => Antares\Users\Auth\BasicThrottle::class,
        /*
          |------------------------------------------------------------------
          | Max attempts
          |------------------------------------------------------------------
          |
          | Define the max attempts allowed before authentication is disabled
          | for the given user.
          |
         */
        'attempts'   => 5,
        /*
          |------------------------------------------------------------------
          | Locked for (in seconds)
          |------------------------------------------------------------------
          |
          | Define number of seconds for the login throttles to disabled
          | user authentication after exceeding max attempts.
          |
         */
        'locked_for' => 60,
    ],
    'notification'                 => [
        'variables' => [
            'admin_list'    => [
                'dataProvider' => 'Antares\Model\User@administrators',
            ],
            'reseller_list' => [
                'dataProvider' => 'Antares\Model\User@resellers',
            ],
            'client_list'   => [
                'dataProvider' => 'Antares\Model\User@clients',
            ],
        ],
    ],
    // Users Activity
    'check_activity_every'         => 60, // in seconds
    'default_profile_picture_path' => '/avatars/default_avatar.png'
];

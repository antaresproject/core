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
    'validation'                 => [
        'required_min_php_version' => '5.5',
        'required_php_extensions'  => [
            'ctype', 'iconv', 'json', 'mcrypt', 'Reflection', 'session', 'zip', 'zlib', 'libxml', 'dom', 'PDO', 'openssl', 'SimpleXML', 'gd', 'mbstring', 'Phar'
        ],
        'required_apache_modules'  => [
            'mod_rewrite', 'mod_filter', 'mod_alias', 'mod_deflate', 'mod_env', 'mod_headers', 'mod_mime'
        ]
    ],
    /**
     * Which commands should be watched and start when not run
     */
    'post_installation_commands' => [
        'notifications:start'
    ],
    'storage_path'               => [
        'ban_management',
        'debugbar',
        'framework/temp',
        'logs',
        'temp'
    ],
    'permissions'                => [
        'roles'      => [
            'member' => [
                'show-dashboard',
                'user-update'
            ],
        ],
        'components' => [
            'core' => [
                'manage-antares'      => 'Allows to manage antares global configuration',
                'manage-users'        => 'Manage administrative users',
                'manage-roles'        => 'Manage roles assigned to users',
                'manage-acl'          => 'Allows to change rules assgined to role',
                'change-app-settings' => 'Change application settings - general configuration section',
                'show-dashboard'      => 'Allows user to view dashboard page after login',
                'Users'               => [
                    'users-list'  => 'View list of users',
                    'user-create' => 'Add new user',
                    'user-update' => 'Update user',
                    'user-delete' => 'Delete user',
                ],
                'Modules and components' => [
                    'component-install'    => 'Install module or component',
                    'component-uninstall'  => 'Uninstall module or component',
                    'component-activate'   => 'Activate (typically run up acl migration) module or component',
                    'component-deactivate' => 'Deactivate (typically run down acl migration) module or component',
                    'component-configure'  => 'Configure module or component',
                ],
                'Branding'               => [
                    'brand-update' => 'Update branding details',
                    'brand-email'  => 'Udate email branding',
                ]
            ],
        ],
    ],
    'fake_users_count'           => 1,
];

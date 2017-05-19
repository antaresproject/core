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
                'manage-antares',
                'manage-users',
                'manage-roles',
                'manage-acl',
                'users-list',
                'user-create',
                'user-update',
                'user-delete',
                'change-app-settings',
                'show-dashboard',
                'component-install',
                'component-uninstall',
                'component-activate',
                'component-deactivate',
                'component-configure',
                'brand-update',
                'brand-email',
            ],
        ],
    ],
    'fake_users_count'           => 1,
];

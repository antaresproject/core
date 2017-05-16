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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






return [
    'di'          => [
        'Antares\Updater\Contracts\IndexPresenter'     => 'Antares\Updater\Http\Presenters\IndexPresenter',
        'Antares\Updater\Contracts\UpdatePresenter'    => 'Antares\Updater\Http\Presenters\UpdatePresenter',
        'Antares\Updater\Contracts\SandboxPresenter'   => 'Antares\Updater\Http\Presenters\SandboxPresenter',
        'Antares\Updater\Contracts\BackupPresenter'    => 'Antares\Updater\Http\Presenters\BackupPresenter',
        'Antares\Updater\Contracts\Resolver'           => 'Antares\Updater\Filesystem\Resolver',
        'Antares\Updater\Contracts\RedAlert'           => 'Antares\Updater\Builder\RedAlert',
        'Antares\Updater\Contracts\Factory'            => 'Antares\Updater\Factory',
        'Antares\Updater\Contracts\Migrator'           => 'Antares\Updater\Strategy\Migrator',
        'Antares\Updater\Contracts\FilesProcessor'     => 'Antares\Updater\Filesystem\Processor\FilesProcessor',
        'Antares\Updater\Contracts\Requirements'       => 'Antares\Updater\Strategy\Sandbox\Requirements',
        'Antares\Updater\Contracts\Database'           => 'Antares\Updater\Strategy\Sandbox\Database',
        'Antares\Updater\Contracts\SandboxFiles'       => 'Antares\Updater\Strategy\Sandbox\Files',
        'Antares\Updater\Contracts\Terminator'         => 'Antares\Updater\Strategy\Sandbox\Terminator',
        'Antares\Updater\Contracts\Rollbacker'         => 'Antares\Updater\Strategy\Sandbox\Rollbacker',
        'Antares\Updater\Contracts\SessionBroadcaster' => 'Antares\Updater\Strategy\Sandbox\SessionBroadcaster',
        'Antares\Updater\Contracts\StorageAdapter'     => 'Antares\Updater\Strategy\Adapter\StorageAdapter',
        'Antares\Updater\Contracts\BackupStrategy'     => 'Antares\Updater\Strategy\Backup\Backup',
        'Antares\Updater\Contracts\Decompressor'       => 'Antares\Updater\Filesystem\Adapter\Decompressor',
        'Antares\Updater\Contracts\DatabaseRestorator' => 'Antares\Updater\Strategy\Backup\DatabaseRestorator',
        'Antares\Updater\Contracts\FilesRestorator'    => 'Antares\Updater\Strategy\Backup\FilesRestorator',
    ],
    /**
     * adapters configuration
     */
    'service'     => [
        'adapters' => [
            'default' => [
                'model' => 'Antares\Updater\Adapter\JsonAdapter',
                'path'  => 'http://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : url()->to('')) . '/service/version.php',
            ]
        ]
    ],
    'scripts'     => [
        'resources' => [
            'install-version' => 'js/update.js'
        ]
    ],
    /**
     * resolver configuration
     */
    'resolver'    => [
        'pattern'      => 'update.json',
        'requirements' => [
            'version', 'description', 'changelog'
        ],
        'model'        => 'Antares\Updater\Model\Version',
        'adapters'     => [
            'default' => [
                'model' => 'Antares\Updater\Filesystem\Adapter\CurlAdapter'
            ]
        ]
    ],
    /**
     * sandbox configuration
     */
    'sandbox'     => [
        'requirements' => [
            'space'       => [
                'min' => 100    // in MB
            ],
            'permissions' => [
                'path' => base_path('builds'),
            ]
        ],
        'database'     => [
            'dumps_path'    => storage_path('app/dumps'),
            'prefix'        => 'build_',
            'character_set' => 'utf8',
            'collation'     => 'utf8_general_ci',
        ],
        'files'        => [
            'build_path'        => base_path('builds'),
            'public_build_path' => base_path('public'),
            'ignore'            => ['.git', 'nbproject', 'builds', 'build', 'vagrant', 'tests', 'database', 'public'],
            'clear'             => [
                'storage/app/backups',
                'storage/app/dumps',
                'storage/app/updates',
                'storage/app/uploads',
                'storage/framework/cache',
                'storage/logs',
                'storage/temp',
                'storage/tickets',
                'storage/tickets/files',
            ],
            'stubs'             => [
                'index.php' => 'public/index.php'
            ]
        ],
        'session'      => [
            'adapters' => [
                'default' => [
                    'model' => \Antares\Updater\Strategy\Adapter\StorageAdapter::class
                ]
            ]
        ]
    ],
    'production'  => [
        'process' => [
            [
                'action'      => 'validate',
                'description' => 'validate sandbox instance...',
            ],
            [
                'action'      => 'backup',
                'description' => 'create backup of production instance...',
            ],
            [
                'action'      => 'start',
                'description' => 'updating system...',
            ],
            [
                'action'      => 'finish',
                'description' => 'finishing migration...',
            ],
        ],
    ],
    'destination' => [

        /*
         * The filesystem(s) you on which the backups will be stored. Choose one or more
         * of the filesystems you configured in app/config/filesystems.php
         */
        'filesystem' => ['local'],
        /*
         * The path where the backups will be saved. This path
         * is relative to the root you configured on your chosen
         * filesystem(s).
         *
         * If you're using the local filesystem a .gitignore file will
         * be automatically placed in this directory so you don't
         * accidentally end up committing these backups.
         */
        'path'       => 'backups/db',
        /*
         * By default the backups will be stored as a zipfile with a
         * timestamp as the filename. With these options You can
         * specify a prefix and a suffix for the filename.
         */
        'prefix'     => '',
        'suffix'     => '',
    ],
];

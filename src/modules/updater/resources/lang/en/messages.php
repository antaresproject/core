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
    'breadcrumb'                         => [
        'sandboxes'      => 'Sandboxes',
        'create_sandbox' => 'Create sandbox',
        'backups'        => 'Backups',
        'create_backup'  => 'Create backup'
    ],
    'create_sandbox_ask'                 => 'Are you sure to create new sandbox instance?',
    'creating_sandbox_description'       => 'This operation may take a while...',
    'ask_for_create_backup'              => 'Are you sure to create backup?',
    'create_backup_description'          => 'Creating new backup. This operation make take a while...',
    'sandbox'                            => [
        'checking_requirements'       => 'checking requirements...',
        'creates_database_instance'   => 'creates database instance',
        'migrating_system_files'      => 'migrating system files',
        'clearing'                    => 'clearing...',
        'preparing_to_open'           => 'preparing to open...',
        'opening_sandbox_mode'        => 'opening sandbox mode...',
        'rollback'                    => 'rollback...',
        'it_make_take_a_while'        => 'It may take a while, please be patient...',
        'create_new_sandbox_instance' => 'Create new sandbox instance...',
        'delete'                      => 'Delete',
        'delete_are_you_sure'         => 'Are you sure?',
        'delete_sandbox_description'  => 'Deleting sandbox instance :version. This operation make take a while...'
    ],
    'backup_success'                     => 'Create backup event has been added to queue. New backup entry should be available on list in few moments.',
    'backup_failed'                      => 'Backup has not been created. More details are available in error logs.',
    'backup_restored_success'            => 'Restore command has been added to queue. Application will be restored in few moments.',
    'restore'                            => 'Restore',
    'are_you_sure_to_restore'            => 'Are you sure?',
    'restoring_application'              => 'Restoring application to backup :name. This operation may take a while.',
    'creating_sandbox_disabled_for_demo' => 'Disabled in our demo environment, sorry',
    'backup_disabled_for_demo'           => 'Disabled in our demo environment, sorry',
    'delete_backup_queue'                => 'Delete',
    'are_you_sure_to_delete_queue'       => 'Are you sure?',
    'deleteing_backup_queue'             => 'Deleteing backup job may reduce the system security.',
    'backup_delete_success'              => 'Backup job has been deleted from queue.',
    'backup_delete_failed'               => 'Backup job has not been deleted from queue.'
];

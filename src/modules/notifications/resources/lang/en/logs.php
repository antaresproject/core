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
 * @package    Notifications
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
return [
    'notification_delete_success'           => 'Notification log has been deleted successfully.',
    'notification_delete_failed'            => 'Notification log has not been deleted.',
    'notification_log'                      => 'Notification Log',
    'filter'                                => [
        'daterange' => 'Date range [:start - :end]',
        'langs'     => 'Languages: %value',
        'names'     => 'Names: %value',
        'areas'     => 'Areas: %value',
    ],
    'headers'                               => [
        'date'  => 'Date',
        'name'  => 'Notification Name',
        'lang'  => 'Language',
        'title' => 'Title',
        'type'  => 'Notification Type',
        'level' => 'User Level',
        'user'  => 'User'
    ],
    'actions'                               => [
        'preview' => 'Preview',
        'delete'  => 'Delete'
    ],
    'are_you_sure'                          => 'Are you sure?',
    'delete_notification_log_desc'          => 'Deleting notification log #:id',
    'mass_deleteing_notification_logs_desc' => 'Deleting selected notification logs...',
    'preview_error'                         => 'Unable to preview notification log.',
    'sidebar_preview'                       => 'Notification preview',
    'notifications_config'                  => 'Notifications',
    'notification_config_save_success'      => 'Notifications configuration has been saved successfully.',
    'form'                                  => [
        'notifications_config_legend'     => 'Notifications configuration',
        'notifications_config_days_label' => 'Remove notification logs every &nbsp;&nbsp; :x &nbsp;&nbsp; days',
        'notifications_config_days_help'  => '* Notification logs will be deleted automatically after X days from the date of creation.<br/>If you leave this field empty logs will not be deleted.'
    ]
];

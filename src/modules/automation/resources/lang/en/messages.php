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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
return [
    'disabled'                    => 'Disabled',
    'enabled'                     => 'Enabled',
    'select_status_placeholder'   => 'select status...',
    'executed_at'                 => 'Exectued at: [:start - :end]',
    'select_date_range'           => 'select date range...',
    'confirm'                     => 'Confirm',
    'datatable'                   => [
        'select_category' => 'Category:',
        'select_all'      => 'All',
        'headers'         => [
            'script_name'     => 'Script name',
            'category'        => 'Category',
            'status'          => 'Status',
            'description'     => 'Description',
            'interval'        => 'Interval',
            'last_run'        => 'Last run',
            'last_run_result' => 'Last run result'
        ]
    ],
    'show_logs'                   => 'Show logs',
    'show_full_log'               => 'Show full log',
    'cancel'                      => 'Cancel',
    'full_log_title'              => 'Details for :script_name',
    'ask'                         => 'Are you sure?',
    'running_job_message'         => 'Running job :name',
    'edit'                        => 'Edit',
    'job'                         => [
        'failed'  => 'Job failed for :name',
        'success' => 'Job success for :name'
    ],
    'intervals'                   => [
        'everyMinute'        => 'Every 1 minute',
        'everyFiveMinutes'   => 'Every 5 minutes',
        'everyTenMinutes'    => 'Every 10 minutes',
        'everyThirtyMinutes' => 'Every 30 minutes',
        'hourly'             => 'Hourly',
        'daily'              => 'Daily',
        'twiceDaily'         => 'Twice daily',
        'twiceDaily'         => 'Twice daily at :value',
        'dailyAt'            => 'Daily at :value',
        'weekly'             => 'Weekly',
        'monthly'            => 'Monthly',
        'quarterly'          => 'Quarterly',
        'yearly'             => 'Yearly'
    ],
    'breadcrumb'                  => [
        'automation_log'           => 'Automation Log',
        'automation_logs_download' => 'Download',
        'automation_logs_delete'   => 'Delete'
    ],
    'deleting_logs_modal_title'   => 'Delete automation logs',
    'select_range_to_delete_logs' => 'Select date range',
    'delete_logs_cancel'          => 'Cancel',
    'delete_logs_delete'          => 'Delete',
    'deleting_logs_modal_title'   => 'Deleting logs',
    'delete_logs_ask'             => 'Are you sure?',
    'automation_delete_success'   => 'Automation logs has been deleted.',
    'automation_delete_error'     => 'Automation logs has not been deleted.',
    'automation_delete_no_logs'   => 'Nothing to delete. There are no automation logs in specified date range.',
    'automation_log'              => 'Automation log'
];

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
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
return [
    /** Whether application use sockets with notifications * */
    'sockets'               => false,
    /** Notifcations and alerts templates * */
    'templates'             => [
        'notification' => 'antares/foundation::layouts.antares.partials._sidebar_notification',
        'alert'        => 'antares/foundation::layouts.antares.partials._sidebar_alert'
    ],
    /** Notifcations priorities * */
    'notification_severity' => [
        'medium'
    ],
    'alert_severity'        => [
        'highest', 'high'
    ],
    'di'                    => [
        \Antares\Notifications\Contracts\IndexPresenter::class => \Antares\Notifications\Http\Presenters\IndexPresenter::class
    ],
    'scripts'               => [
        'resources-rich'    => [
        //'ckeditor-js' => 'packages/ckeditor/ckeditor.js'
        ],
        'default'           => [
            'brain-socket-js' => 'js/brain-socket.min.js',
            'socket-js'       => 'js/socket.js'
        ],
        'resources-default' => [
            'notifications-js' => 'js/default.js',
        ],
        'position'          => 'antares/foundation::scripts'
    ],
    'template'              => [
        'defaults' => [
            'list' => 'template.list'
        ]
    ],
    'default'               => [
        'sms'                             => '48697274132',
        'custom_event'                    => 'antares.notifier.events.custom',
        'notifications_remove_after_days' => 90
    ],
];

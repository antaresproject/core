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
    'use_gravatar'                => 'Use gravatar instead',
    'extensions' => [
        'modal_title' => [
            'install'       => 'Extension installation',
            'activate'      => 'Extension activation',
            'deactivate'    => 'Extension deactivation',
            'uninstall'     => 'Extension uninstallation',
        ],
        'modal_content' => [
            'install'       => 'The :name extension will be installed. Do you want to proceed?',
            'activate'      => 'The :name extension will be activated. Do you want to proceed?',
            'deactivate'    => 'The :name extension will be deactivated. Do you want to proceed?',
            'uninstall'     => 'The :name extension will be uninstalled. Do you want to proceed?',
        ],
    ],
    'confirm'                     => 'Confirm',
    'select_placeholder_default'  => 'select :name...',
    'select_extension_type'       => 'Type',
    'extension_type_all'          => 'All',
    'sidebar'                     => [
        'notifications'  => 'Notifications',
        'alerts'         => 'Alerts',
        'no_alerts'      => 'No alerts available...',
        'no_items_found' => 'No items found...'
    ],
    'logged_out_from_user'        => 'You have been logout successfully from user session.',
    'logged_as_user'              => 'You have been login into user :name session.',
    'yes'                         => 'Yes',
    'no'                          => 'No',
    'statuses'                    => [
        'all'      => 'All',
        'active'   => 'Active (:count)',
        'disabled' => 'Disabled (:count)'
    ],
    'notifier_mail_has_been_sent' => 'An e-mail notification has been sent to recipient.',
    'are_you_sure'                => 'Are you sure?',
    'form'                        => [
        'help' => [
            'driver'           => '*Mail drivers such as SMTP and sendmail, allowing to sending mail through a local or cloud based service of your choice.',
            'email_address'    => '*Outgoing email address (sender email).',
            'email_host'       => '*The host name of outgoing SMTP (eg.: smtp.gmail.com).',
            'email_port'       => '*The default port number of an outgoing SMTP server. Typically Common SMTP ports: SMTP - port 25 or 2525 or 587, Secure SMTP (SSL / TLS) - port 465 or 25 or 587, 2526 (Elastic Email).',
            'email_username'   => 'SMTP server username credential.',
            'email_password'   => 'SMTP server password credential.',
            'email_encryption' => 'Encryption of email messages to protect the content from being read by other entities than the intended recipients.',
            'email_sendmail'   => 'Sendmail is a most popular SMTP server used in most of Linux/Unix distribution. Sendmail allows sending email as simple command.'
        ]
    ]
];




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
    'providers' => [
        'smtp'     => [
            'title'  => 'SMTP',
            'fields' => [
                'email_address'    => ['type' => 'input:text', 'validators' => ['required']],
                'email_host'       => ['type' => 'input:text', 'validators' => ['required']],
                'email_port'       => ['type' => 'input:text', 'validators' => ['required']],
                'email_username'   => ['type' => 'input:text', 'validators' => ['required']],
                'email_password'   => ['type' => 'input:password', 'validators' => ['required']],
                'email_encryption' => [
                    'type'       => 'select',
                    'validators' => ['required'],
                    'options'    => [
                        ''    => 'None...',
                        'SSL' => 'SSL',
                        'TLS' => 'TLS'
                    ],
                    'fieldClass' => 'w150'
                ],
            ]
        ],
        'sendmail' => [
            'title'  => 'Sendmail',
            'fields' => [
                'email_sendmail' => ['type' => 'input:text', 'validators' => ['required'], 'default' => '/usr/sbin/sendmail -bs'],
            ]
        ]
    ]
];

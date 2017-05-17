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


use Antares\Notifier\Adapter\EmailAdapter;
use Antares\Notifier\Adapter\FastSmsAdapter;

return [
    'default' => 'laravel',
    'email'   => [
        'adapters' => [
            'default'     => 'swiftMailer',
            'swiftMailer' => [
                'model' => EmailAdapter::class,
            ]
        ]
    ],
    'system'  => [
        'adapters' => [
            'default'     => 'swiftMailer',
            'swiftMailer' => [
                'model' => EmailAdapter::class,
            ]
        ]
    ],
    'sms'     => [
        'adapters' => [
            'default' => 'fastSms',
            'fastSms' => [
                'api'         => [
                    'token'    => '6kzF-p26E-xnPS-Y1bL',
                    'login'    => 'mariusz@modulesgarden.com',
                    'password' => 'ZAQ!2wsx',
                    'url'      => 'https://my.fastsms.co.uk/api',
                ],
                'name'        => 'Fast SMS',
                'provider'    => '<a href="http://www.fastsms.co.uk/?a_aid=559b889916b99">www.fastsms.co.uk</a>',
                'link'        => '<a href="http://www.fastsms.co.uk/?a_aid=559b889916b99" target="_blank" style=" color: #4169E1;">Sign up for a free account</a> and get ten credits to try out Fastsms services. No billing info required!',
                'img'         => 'https://my.fastsms.co.uk/design/img/resellers/6/logo1.png',
                'description' => 'Fastsms is a top professional in the field of internet texting solutions as their structure covers more than 500 mobile networks in over 200 countries. What gives them a decided edge over other providers is a transparent pricing policy and unprecedented flexibility in the use of their services. There are no set-up costs or minimum usage requirements in Fastsms – you simply pay for the blocks of outgoing text credits and use them whenever you wish, as they have no expiry date.',
                'model'       => FastSmsAdapter::class,
                'codes'       => [
                    '-100' => 'Not Enough Credits',
                    '-101' => 'Invalid CreditID',
                    '-200' => 'Invalid Contact',
                    '-300' => 'General Database Error',
                    '-301' => 'Unknown Error',
                    '-302' => 'Return XML Error',
                    '-303' => 'Received XML Error',
                    '-400' => 'Some numbers in list failed',
                    '-401' => 'Invalid Destination Address',
                    '-402' => 'Invalid Source Address – Alphanumeric too long',
                    '-403' => 'Invalid Source Address – Invalid Number',
                    '-404' => 'Blank Body',
                    '-405' => 'Invalid Validity Period',
                    '-406' => 'No Route Available',
                    '-407' => 'Invalid Schedule Date',
                    '-408' => 'Distribution List is Empty',
                    '-409' => 'Group is Empty',
                    '-410' => 'Invalid Distribution List',
                    '-411' => 'You have exceeded the limit of messages you can send in a single day to a single number',
                    '-412' => 'Number is blacklisted',
                    '-414' => 'Invalid Group',
                    '-501' => 'Unknown Username/Password',
                    '-502' => 'Unknown Action',
                    '-503' => 'Unknown Message ID',
                    '-504' => 'Invalid From Timestamp',
                    '-505' => 'Invalid To Timestamp',
                    '-506' => 'Source Address Not Allowed (Email2SMS)',
                    '-507' => 'Invalid/Missing Details',
                    '-508' => 'Error Creating User',
                    '-509' => 'Unknown/Invalid User',
                    '-510' => 'You cannot set a user’s credits to be less than 0',
                    '-511' => 'The system is down for maintenance',
                    '-512' => 'User Suspended',
                    '-513' => 'License in use',
                    '-514' => 'License expired',
                    '-515' => 'No License available',
                    '-516' => 'Unknown List',
                    '-517' => 'Unable to create List',
                    '-518' => 'Blank or Invalid Source Address',
                    '-519' => 'Blank Message Body',
                    '-520' => 'Unknown Group',
                    '-601' => 'Unknown Report Type',
                    '-701' => 'No UserID Specified',
                    '-702' => 'Invalid Amount Specified',
                    '-703' => 'Invalid Currency Requested'
                ]
            ]
        ]
    ]
];

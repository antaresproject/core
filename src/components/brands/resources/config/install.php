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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


return [
    'default'  => [
        'colors'      => [
            'main'       =>
            [
                'value' => '#02a8f3',
                'mod1'  => '#029de4',
                'mod2'  => '#0fb5ff',
                'mod3'  => '#1bc1ff',
            ],
            'secondary'  =>
            [
                'value' => '#30343d',
                'mod1'  => '#292d34',
                'mod2'  => '#434853',
                'mod3'  => '#8c9099',
            ],
            'background' =>
            [
                'value' => '#70c24a',
                'mod1'  => '#4d8035',
                'mod2'  => '#69ae4a',
                'mod3'  => '#7cbe5d',
            ],
            'text'       =>
            [
                'main'       =>
                [
                    'first'  => '#ffffff',
                    'second' => '#ffffff',
                ],
                'secondary'  =>
                [
                    'first'  => '#8a9099',
                    'second' => '#ffffff',
                ],
                'background' =>
                [
                    'first'  => '#8a9099',
                    'second' => '#000000',
                ],
            ],
        ],
        'logo'        => 'logo_default_full.png',
        'favicon'     => 'logo_default_tear.png',
        'composition' => 'small_sidemenu',
        'styleset'    => 'material'
    ],
    'optional' => [
        'colors'      => [

            'main'       =>
            [
                'value' => '#fa6464',
                'mod1'  => '#fa5555',
                'mod2'  => '#ff7171',
                'mod3'  => '#ff7d7d',
            ],
            'secondary'  =>
            [
                'value' => '#963c3c',
                'mod1'  => '#8b3838',
                'mod2'  => '#ac4e4e',
                'mod3'  => '#f29898',
            ],
            'background' =>
            [
                'value' => '#fa6464',
                'mod1'  => '#ed1a1a',
                'mod2'  => '#f15858',
                'mod3'  => '#f57878',
            ],
            'text'       =>
            [
                'main'       =>
                [
                    'first'  => '#ffffff',
                    'second' => '#fa6464',
                ],
                'secondary'  =>
                [
                    'first'  => '#ffffff',
                    'second' => '#fa6464',
                ],
                'background' =>
                [
                    'first'  => '#fa6464',
                    'second' => '#fa6464',
                ],
            ],
        ],
        'logo'        => 'users_logo_default_full.png',
        'favicon'     => 'users_logo_default_tear.png',
        'composition' => 'big_sidemenu',
        'styleset'    => 'material'
    ]
];

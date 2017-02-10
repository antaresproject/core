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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


return [
    'validation'     => [
        'host'     => 'http://192.168.1.217/',
        'path'     => '/license/checkout',
        'port'     => '80',
        /**
         * how often license should be verified (in hours)
         */
        'interval' => '4',
        'alert'    => [
            'adapter' => 'Antares\Licensing\Adapter\LicenseNotifier',
        ]
    ],
    'redirect_route' => 'license/invalid',
    'translations'   => [
        'SOCKET_FAILED' => 'Unable to connect to license server',
    ],
    /**
     * hash key 1 used to encrypt the generate key data.
     * hash key 2 used to encrypt the request data
     * hash key 3 used to encrypt the dial home data
     * NOTE1 : there are three different hash keys for the three different operations
     * NOTE2 : these hash key's are for use by both mcrypt and alternate cryptions
     *       and although mcrypts keys are typically short they should be kept long
     *      for the sake of the other functions     
     */
    'cryptor'        => [
        'hashKey1'  => 'YmUzYWM2sNGU24NbA363zA7IDSDFGDFGB5aVi35BDFGQ3YNO36ycDFGAATq4sYmSFVDFGDFGps7XDYEzGDDw96OnMW3kjCFJ7M+UV2kHe1WTTEcM09UMHHT',
        'hashKey2'  => '80dSbqylf4Cu5e5OYdAoAVkzpRDWAt7J1Vp27sYDU52ZBJprdRL1KE0il8KQXuKCK3sdA51P9w8U60wohX2gdmBu7uVhjxbS8g4y874Ht8L12W54Q6T4R4a',
        'hashKey3'  => 'ant9pbc3OK28Li36Mi4d3fsWJ4tQSN4a9Z2qa8W66qR7ctFbljsOc9J4wa2Bh6j8KB3vbEXB18i6gfbE0yHS0ZXQCceIlG7jwzDmN7YT06mVwcM9z0vy62T',
        'useMcrypt' => true,
        'algorithm' => 'blowfish'
    ],
    'types'          => [
        'trial', 'lite', 'standard', 'ultimate', 'enterprise'
    ],
];

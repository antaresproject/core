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
    'per_page'     => 10,
    'logo'         => [
        'default_path' => '/_dist/img/theme/antares/logo/',
        'destination'  => public_path('img/logos'),
        'rules'        => [
            "acceptedFiles" => ['jpg', 'png', 'jpeg'],
            "maxFilesize"   => 9.765625,
            "minFilesize"   => 0.0009765625,
            'dimensions'    => [
                'main'    => [
                    'min_width' => 190,
                    'max_width' => 192,
                ],
                'favicon' => [
                    'min_width'  => 60,
                    'min_height' => 60
                ],
            ]
        ],
    ],
    /**
     * default values for brand options
     */
    'default'      => [
        'header' => view('antares/brands::_header_default'),
        'styles' => view('antares/brands::_styles_default'),
        'footer' => view('antares/brands::_footer_default'),
    ],
    'compositions' => [
        'small_sidemenu', 'big_sidemenu', 'upper_menu'
    ],
    'stylesets'    => [
        'material', 'simple'
    ],
];


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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
return [
    /**
     * definition of cache key
     */
    'cache'     => 'ui-components',
    /**
     * gridstack resources & scripts configuration
     */
    'gridstack' => [
        'resources'   => [
            'on-load-gridstack' => 'js/on-load.js',
        ],
        'placeholder' => 'antares/foundation::scripts'
    ],
    /**
     * default ui components configuration
     */
    'defaults'  => [
        'ignored'    => [
            'fixed_width',
            'fixed_height',
            'min_width',
            'min_height',
            'max_width',
            'max_height',
            'default_width',
            'default_height',
            'resizable',
            'draggable',
            'nestable',
            'titlable',
            'editable',
            'removable',
            'manually_disabled',
            'enlargeable',
            'ajaxable',
            'zoomable',
            'card_class',
            'card_content_class',
            'actions'
        ],
        'attributes' => [
            'x'                 => 0,
            'y'                 => 0,
            'enlargeable'       => false,
            'fixed_width'       => false,
            'fixed_height'      => false,
            'min_width'         => 3,
            'min_height'        => 3,
            'max_width'         => 52,
            'max_height'        => 52,
            'default_width'     => 3,
            'default_height'    => 3,
            'resizable'         => true,
            'draggable'         => true,
            'nestable'          => false,
            'titlable'          => false,
            'editable'          => true,
            'removable'         => true,
            'disabled'          => true,
            'manually_disabled' => false,
            'actions'           => false
        ]
    ],
    /**
     * template configuration
     */
    'templates' => [
        'public_path'      => 'ui-components/templates',
        'preview_pattern'  => 'screenshot.png',
        'preview_default'  => 'img/screenshot.png',
        'manifest_pattern' => 'template.json',
        'indexes_path'     => 'resources/views/templates'
    ],
    /**
     * resources ignored by ui component middleware
     */
    'ignore'    => [
        'Antares\UI\UIComponents\Http\Controllers\Admin\DefaultController' => [
            'view', 'grid', 'positions'
        ],
        'Antares\UI\UIComponents\Http\Controllers\Admin\UpdateController'  => [
            'edit', 'update'
        ],
        'Antares\Foundation\Http\Controllers\CredentialController'         => [
            'logout'
        ],
        'Antares\Updater\Http\Controllers\Admin\IndexController'           => [
            'update'
        ]
    ],
];

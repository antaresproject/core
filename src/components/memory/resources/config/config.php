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
    'permission' => [
        'cache_prefix' => 'permission',
    ],
    'driver'     => 'component.default',
    'cache'      => [],
    'primary'    => [
        'model' => '\Antares\Memory\Model\Option',
        'cache' => false,
    ],
    'eloquent'   => [
        'default' => [
            'model' => '\Antares\Memory\Model\Test',
            'cache' => false
        ],
    ],
    'component'  => [
        'default' => [
            'model' => 'Antares\Memory\Model\Permission',
            'cache' => false,
        ],
    ],
    'fluent'     => [
        'default' => [
            'table' => 'tbl_antares_options',
            'cache' => false,
        ],
    ],
    'registry'   => [
        'default' => [
            'model' => '\Antares\Memory\Model\Test',
            'cache' => false,
        ],
        'forms'   => [
            'model' => '\Antares\Memory\Model\Forms',
            'cache' => false,
        ],
    ],
    'runtime'    => [],
];

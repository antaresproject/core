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

    'icon-mapping' => [
        'success' => 'fa-check-circle',
        'danger'  => 'fa-bell-slash',
        'warning' => 'fa-warning',
        'error'   => 'fa-bell-slash'
    ],
    'scripts'      => [
        'resources'   => [
            'flash.messenger' => ['packages/core/js/flash-messenger.js', 'noty.packaged'],
        ],
        'placeholder' => 'antares/foundation::scripts'
    ],
];

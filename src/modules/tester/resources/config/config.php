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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



return [
    'di'        => [
        'Antares\Tester\Contracts\Builder' => 'Antares\Tester\Builder\RoundRobin'
    ],
    'memory'    => [
        'model' => '\Antares\Tester\Model\MemoryTests'
    ],
    'view'      => 'antares/tester::admin.partials._button',
    'container' => 'antares/foundation::scripts',
    'inputId'   => 'tester-button',
    'codes'     => require_once('codes.php')
];

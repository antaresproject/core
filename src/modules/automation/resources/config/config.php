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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



return [
    'memory'  => [
        'model' => \Antares\Automation\Model\Jobs::class
    ],
    'ignored' => [
        'automation:start',
        'queue:work',
        'queue:start',
        'automation:sync'
    ]
];

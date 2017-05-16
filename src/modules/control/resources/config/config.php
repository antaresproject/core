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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



use Antares\Control\Contracts\Command\Synchronizer as SynchronizerContract;
use Antares\Control\Contracts\ControlsAdapter as ControlsAdapterContract;
use Antares\Control\Contracts\ModulesAdapter as ModulesAdapterContract;
use Antares\Control\Adapter\ControlsAdapter;
use Antares\Control\Adapter\ModulesAdapter;
use Antares\Control\Command\Synchronizer;

return [
    'allow_register_with_other_roles' => false,
    'di'                              => [
        SynchronizerContract::class    => Synchronizer::class,
        ControlsAdapterContract::class => ControlsAdapter::class,
        ModulesAdapterContract::class  => ModulesAdapter::class
    ],
    'localtime'                       => [
        'enable' => false,
    ],
    'memory'                          => [
        'default' => [
            'model' => 'Antares\Control\Model\Middleware',
            'cache' => false,
            'crypt' => true
        ]
    ]
];

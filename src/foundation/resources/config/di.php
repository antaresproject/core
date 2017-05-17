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
    'Antares\UI\UIComponents\Contracts\GridStack'               => 'Antares\UI\UIComponents\Adapter\GridStackAdapter',
    'Antares\Tester\Contracts\Extractor'                        => 'Antares\Tester\Adapter\ExtractAdapter',
    'Antares\Tester\Contracts\ClassValidator'                   => 'Antares\Tester\Validator\ClassValidator',
    'Antares\Contracts\Http\Middleware\ModuleNamespaceResolver' => 'Antares\Foundation\Http\Resolver\ModuleNamespaceResolver'
];

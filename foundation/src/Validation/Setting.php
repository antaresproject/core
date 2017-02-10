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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Validation;

use Antares\Support\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorResolver;

class Setting extends Validator
{

    /**
     * List of rules.
     *
     * @var array
     */
    protected $rules = [
        'site_name' => ['required'],
    ];

    /**
     * List of events.
     *
     * @var array
     */
    protected $events = [
        'antares.validate: settings',
    ];

}

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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Http\Validators;

use Antares\Support\Validator;

class FieldValidator extends Validator
{

    /**
     * List of rules.
     * @var array
     */
    protected $rules = [
        'name'             => ['required', 'regex:[^[a-z0-9_]{2,50}$]'],
        'validator_custom' => ['validator-list']
    ];

    /**
     * List of events.
     *
     * @var array
     */
    protected $events = [
        'antares.validate: customfields',
    ];

    /**
     * On create validations.
     * @return void
     */
    protected function onCreate()
    {
        $this->rules['name']             = ['required', 'name-on-create', 'regex:[^[a-z0-9_]{2,50}$]'];
        $this->rules['validator_custom'] = ['validator-list'];
    }

    /**
     * On update validations.
     * @return void
     */
    protected function onUpdate()
    {
        $this->rules['name'][] = 'required';
    }

    /**
     * On update validations.
     * @return void
     */
    protected function onImported()
    {
        $this->rules['name'] = [];
    }

}

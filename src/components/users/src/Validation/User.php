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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Users\Validation;

use Antares\Support\Validator;

class User extends Validator
{

    /**
     * List of rules.
     *
     * @var array
     */
    protected $rules = [
        'email'     => ['required', 'email'],
        'firstname' => ['required'],
        'lastname'  => ['required'],
    ];

    /**
     * List of events.
     *
     * @var array
     */
    protected $events = [
        'antares.validate: users',
        'antares.validate: user.account',
    ];

    /**
     * validation messages
     *
     * @var array
     */
    protected $phrases = [
        'required' => 'Field is required and cannot be empty.'
    ];

    /**
     * On create user scenario.
     *
     * @return void
     */
    protected function onCreate()
    {
        $this->rules['password'] = ['required'];
        $this->rules['email']    = ['required', 'email', 'unique:tbl_users,id'];
    }

}

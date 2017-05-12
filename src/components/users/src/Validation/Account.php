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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Users\Validation;

use Antares\Support\Validator;

class Account extends Validator
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
        'antares.validate: user.account',
    ];

    /**
     * On register scenario.
     *
     * @return void
     */
    protected function onRegister()
    {
        $this->rules = array_replace($this->rules, [
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['sometimes', 'required'],
            'password_confirmation' => ['sometimes', 'same:password'],
        ]);

        $this->events[] = 'antares.validate: user.account.register';
    }

    /**
     * On update password scenario.
     *
     * @return void
     */
    protected function onChangePassword()
    {
        $this->rules = [
            'current_password'      => ['required'],
            'new_password'          => ['required', 'different:current_password'],
            'password_confirmation' => ['same:new_password'],
        ];

        $this->events = [];
    }

    /**
     * On update user details
     */
    public function onUpdate()
    {
        $this->rules['password_confirmation'] = ['required', 'same:password'];
        return $this;
    }

}

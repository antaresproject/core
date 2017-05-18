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

class AuthenticateUser extends Validator
{

    /**
     * List of rules.
     *
     * @var array
     */
    protected $rules = [
        'email' => ['required', 'email', 'exists:tbl_users,email']
    ];

    /**
     * On login scenario.
     *
     * @return void
     */
    protected function onLogin()
    {
        $this->rules['password'] = ['required'];
    }

    /**
     * On api login scenario.
     *
     * @return void
     */
    protected function onApi()
    {
        $this->rules['email'] = ['required', 'email', 'exists:tbl_users,email', 'custom'];
    }

}

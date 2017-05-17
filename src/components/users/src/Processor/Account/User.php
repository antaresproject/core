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


namespace Antares\Users\Processor\Account;

use Antares\Users\Http\Presenters\Account as Presenter;
use Antares\Users\Validation\Account as Validator;
use Antares\Foundation\Processor\Processor;
use Illuminate\Support\Facades\Event;

abstract class User extends Processor
{

    /**
     * Create a new processor instance.
     *
     * @param  \Antares\Foundation\Http\Presenters\Account  $presenter
     * @param  \Antares\Foundation\Validation\Account  $validator
     */
    public function __construct(Presenter $presenter, Validator $validator)
    {
        $this->presenter = $presenter;
        $this->validator = $validator;
    }

    /**
     * Validate current user.
     *
     * @param  \Antares\Model\User|\Illuminate\Database\Eloquent\Model  $user
     * @param  array  $input
     *
     * @return bool
     */
    protected function validateCurrentUser($user, array $input)
    {
        return (string) $user->getAttribute('id') === $input['id'];
    }

    /**
     * Fire Event related to eloquent process.
     *
     * @param  string  $type
     * @param  array   $parameters
     *
     * @return void
     */
    protected function fireEvent($type, array $parameters = [])
    {
        Event::fire("antares.{$type}: user.account", $parameters);
    }

    /**
     * Fire Event related to eloquent process and customfields.
     *
     * @param  string  $type
     * @param  array   $parameters
     *
     * @return void
     */
    protected function fireCustomFieldsEvent($type, array $parameters = [])
    {
        Event::fire("antares.form: user.{$type}", $parameters);
    }

}

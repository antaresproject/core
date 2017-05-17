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


namespace Antares\Users\Processor;

use Antares\Foundation\Processor\Processor;
use Antares\Contracts\Auth\Guard;

abstract class Authenticate extends Processor
{

    /**
     * The auth guard implementation.
     *
     * @var \Antares\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Create a new processor instance.
     *
     * @param  \Antares\Contracts\Auth\Guard  $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Get user.
     *
     * @return \Antares\Model\User|null
     */
    protected function getUser()
    {
        return $this->auth->getUser();
    }

}

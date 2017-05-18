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


namespace Antares\Twig\Extension\Laravel;

use Twig_Extension;
use Twig_SimpleFunction;
use Illuminate\Auth\AuthManager;

/**
 * Access Laravels auth class in your Twig templates.
 */
class Auth extends Twig_Extension
{

    /**
     * @var \Illuminate\Auth\AuthManager
     */
    protected $auth;

    /**
     * Create a new auth extension.
     *
     * @param \Illuminate\Auth\AuthManager
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Auth';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('auth_check', [$this->auth, 'check']),
            new Twig_SimpleFunction('auth_guest', [$this->auth, 'guest']),
            new Twig_SimpleFunction('auth_user', function () {
                        return $this->auth->user();
                    }),
        ];
    }

}

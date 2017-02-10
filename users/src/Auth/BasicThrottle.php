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


namespace Antares\Users\Auth;

use Illuminate\Cache\RateLimiter;
use Antares\Contracts\Auth\Command\ThrottlesLogins as Command;

class BasicThrottle extends ThrottlesLogins implements Command
{

    /**
     * The cache limiter implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cacheLimiter;

    /**
     * Construct a new processor.
     *
     * @param  \Illuminate\Cache\RateLimiter  $cacheLimiter
     */
    public function __construct(RateLimiter $cacheLimiter)
    {
        $this->cacheLimiter = $cacheLimiter;
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @return bool
     */
    public function hasTooManyLoginAttempts()
    {
        return $this->cacheLimiter->tooManyAttempts(
                        $this->getUniqueLoginKey(), $this->maxLoginAttempts(), $this->lockoutTime() / 60
        );
    }

    /**
     * Get total seconds before doing another login attempts for the user.
     *
     * @param  array  $input
     *
     * @return int
     */
    public function getSecondsBeforeNextAttempts()
    {
        return (int) $this->cacheLimiter->availableIn($this->getUniqueLoginKey());
    }

    /**
     * Increment the login attempts for the user.
     *
     * @return void
     */
    public function incrementLoginAttempts()
    {
        $this->cacheLimiter->hit($this->getUniqueLoginKey());
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @return void
     */
    public function clearLoginAttempts()
    {
        $this->cacheLimiter->clear($this->getUniqueLoginKey());
    }

}

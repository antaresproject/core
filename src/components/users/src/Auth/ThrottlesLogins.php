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

use Illuminate\Support\Arr;
use Illuminate\Http\Request;

abstract class ThrottlesLogins
{

    /**
     * The configurations.
     *
     * @var array
     */
    protected static $config = [
        'attempts'   => 5,
        'locked_for' => 60,
    ];
    protected $loginKey      = 'email';

    /**
     * The HTTP Requesr object.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Set configuration.
     *
     * @param  array  $config
     *
     * @return void
     */
    public static function setConfig(array $config)
    {
        static::$config = array_merge(static::$config, $config);
    }

    /**
     * Set HTTP Request object.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set user login key.
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function setLoginKey($key)
    {
        $this->loginKey = $key;

        return $this;
    }

    /**
     * Get the maximum number of login attempts for delaying further attempts.
     *
     * @return int
     */
    protected function maxLoginAttempts()
    {
        return Arr::get(static::$config, 'attempts', 5);
    }

    /**
     * The number of seconds to delay further login attempts.
     *
     * @return int
     */
    protected function lockoutTime()
    {
        return Arr::get(static::$config, 'locked_for', 60);
    }

    /**
     * Get the login key.
     *
     * @return string
     */
    protected function getUniqueLoginKey()
    {
        $key = $this->request->input($this->loginKey);
        $ip  = $this->request->ip();

        return $key . $ip;
    }

}

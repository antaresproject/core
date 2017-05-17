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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Html\Support\Traits;

use Illuminate\Session\Store;

trait SessionHelperTrait
{
    /**
     * The CSRF token used by the form builder.
     *
     * @var string
     */
    protected $csrfToken;

    /**
     * The session store implementation.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Get a value from the session's old input.
     *
     * @param  string  $name
     *
     * @return string
     */
    public function old($name)
    {
        if (isset($this->session)) {
            return $this->session->getOldInput($this->transformKey($name));
        }
    }

    /**
     * Determine if the old input is empty.
     *
     * @return bool
     */
    public function oldInputIsEmpty()
    {
        return (isset($this->session) && count($this->session->getOldInput()) == 0);
    }

    /**
     * Get the session store implementation.
     *
     * @return  \Illuminate\Session\Store  $session
     */
    public function getSessionStore()
    {
        return $this->session;
    }

    /**
     * Set the session store implementation.
     *
     * @param  \Illuminate\Session\Store  $session
     *
     * @return $this
     */
    public function setSessionStore(Store $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Generate a hidden field with the current CSRF token.
     *
     * @return string
     */
    abstract public function token();

    /**
     * Transform key from array to dot syntax.
     *
     * @param  string  $key
     *
     * @return string
     */
    abstract protected function transformKey($key);
}

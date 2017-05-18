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


namespace Antares\Messages;

use Closure;
use Illuminate\Session\Store as SessionStore;
use Illuminate\Support\MessageBag as Message;
use Antares\Contracts\Messages\MessageBag as MessageBagContract;

class MessageBag extends Message implements MessageBagContract
{

    /**
     * The session store instance.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Cached messages to be extends to current request.
     *
     * @var static
     */
    protected $instance;

    /**
     * Set the session store.
     *
     * @param  \Illuminate\Session\Store   $session
     *
     * @return $this
     */
    public function setSessionStore(SessionStore $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Get the session store.
     *
     * @return \Illuminate\Session\Store
     */
    public function getSessionStore()
    {
        return $this->session;
    }

    /**
     * Extend Messages instance from session.
     *
     * @param  \Closure $callback
     *
     * @return static
     */
    public function extend(Closure $callback)
    {
        $instance = $this->retrieve();
        call_user_func($callback, $instance);

        return $instance;
    }

    /**
     * Retrieve Message instance from Session, the data should be in
     * serialize, so we need to unserialize it first.
     *
     * @return static
     */
    public function retrieve()
    {
        $messages = null;
        if (!isset($this->instance)) {
            $this->instance = new static();
            $this->instance->setSessionStore($this->session);
            if ($this->session->has('message')) {
                $messages = unserialize($this->session->pull('message'));
            }

            if (is_array($messages)) {
                $this->instance->merge($messages);
            }
        }

        return $this->instance;
    }

    /**
     * Store current instance.
     *
     * @return void
     */
    public function save()
    {
        $this->session->flash('message', $this->serialize());
    }

    /**
     * Compile the instance into serialize.
     *
     * @return string   serialize of this instance
     */
    public function serialize()
    {
        return serialize($this->messages);
    }

}

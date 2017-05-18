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

namespace Antares\Memory;

use Antares\Support\Traits\DataContainerTrait;
use Antares\Contracts\Memory\Handler as HandlerContract;
use Antares\Contracts\Memory\Provider as ProviderContract;

class Provider implements ProviderContract
{

    use DataContainerTrait;

    /**
     * Handler instance.
     *
     * @var \Antares\Contracts\Memory\Handler
     */
    protected $handler;

    /**
     * Construct an instance.
     *
     * @param  \Antares\Contracts\Memory\Handler  $handler
     */
    public function __construct(HandlerContract $handler)
    {

        $this->handler = $handler;
        $this->items   = $this->handler->initiate();
    }

    /**
     * Get handler instance.
     *
     * @return \Antares\Contracts\Memory\Handler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Shutdown/finish method.
     *
     * @return bool
     */
    public function finish()
    {
        return $this->handler->finish($this->items);
    }

    /**
     * updates method
     */
    public function update()
    {
        return $this->handler->update($this->items);
    }

    /**
     * updates method
     */
    public function setup()
    {
        return $this->handler->setup($this->items);
    }

    /**
     * updates method
     */
    public function raw($params = null)
    {
        return $this->handler->raw($params);
    }

    /**
     * Set a value from a key.
     *
     * @param  string  $key    A string of key to add the value.
     * @param  mixed   $value  The value.
     *
     * @return mixed
     */
    public function put($key, $value = '')
    {
        $this->set($key, $value);

        return $value;
    }

    /**
     * get item from handler by key
     * 
     * @param mixed $name
     * @return mixed
     */
    public function find($name)
    {
        return $this->getHandler()->get($name);
    }

    public function items()
    {
        return $this->getHandler()->raw();
    }

}

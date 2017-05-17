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

use Antares\Contracts\Memory\Provider as ProviderContract;

trait ContainerTrait
{
    /**
     * Memory instance.
     *
     * @var \Antares\Contracts\Memory\Provider
     */
    protected $memory = null;

    /**
     * Check whether a Memory instance is already attached to the container.
     *
     * @return bool
     */
    public function attached()
    {
        return ($this->memory instanceof ProviderContract);
    }

    /**
     * Attach memory provider.
     *
     * @param  \Antares\Contracts\Memory\Provider  $memory
     *
     * @return object
     */
    public function attach(ProviderContract $memory)
    {
        $this->setMemoryProvider($memory);

        return $this;
    }

    /**
     * Set memory provider.
     *
     * @param  \Antares\Contracts\Memory\Provider  $memory
     *
     * @return object
     */
    public function setMemoryProvider(ProviderContract $memory)
    {
        $this->memory = $memory;

        return $this;
    }

    /**
     * Set memory provider.
     *
     * @return \Antares\Contracts\Memory\Provider|null
     */
    public function getMemoryProvider()
    {
        return $this->memory;
    }
}

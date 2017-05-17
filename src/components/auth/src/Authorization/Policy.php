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


namespace Antares\Authorization;

use Antares\Contracts\Authorization\Factory as FactoryContract;

abstract class Policy
{

    /**
     * The authorization implementation.
     *
     * @var \Antares\Contracts\Authorization\Authorization
     */
    protected $acl;

    /**
     * Authorization driver name.
     *
     * @var string
     */
    protected $name;

    /**
     * Set authorization driver.
     *
     * @param  \Antares\Contracts\Authorization\Factory  $factory
     *
     * @return $this
     */
    public function setAuthorization(FactoryContract $factory)
    {
        $this->acl = $factory->make($this->getAuthorizationName());

        return $this;
    }

    /**
     * Get authorization driver.
     *
     * @return \Antares\Contracts\Authorization\Authorization
     */
    protected function getAuthorization()
    {
        return $this->acl;
    }

    /**
     * Resolve if authorization can.
     *
     * @param  string  $action
     *
     * @return bool
     */
    protected function can($action)
    {
        return $this->acl->can($action);
    }

    /**
     * Resolve if authorization can if action exists.
     *
     * @param  string  $action
     *
     * @return bool
     */
    protected function canIf($action)
    {
        return $this->acl->canIf($action);
    }

    /**
     * Get authorization driver name.
     *
     * @return string
     */
    protected function getAuthorizationName()
    {
        return $this->name ? : 'antares';
    }

}

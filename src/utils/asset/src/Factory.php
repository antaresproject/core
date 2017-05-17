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


namespace Antares\Asset;

class Factory
{

    /**
     * Asset Dispatcher instance.
     *
     * @var \Antares\Asset\Dispatcher
     */
    protected $dispatcher;

    /**
     * All of the instantiated asset containers.
     *
     * @var array
     */
    protected $containers = [];

    /**
     * Construct a new environment.
     *
     * @param  \Antares\Asset\Dispatcher  $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get an asset container instance.
     *
     * <code>
     *     // Get the default asset container
     *     $container = Antares\Asset::container();
     *
     *     // Get a named asset container
     *     $container = Antares\Asset::container('footer');
     * </code>
     *
     * @param  string  $container
     *
     * @return \Antares\Asset\Asset
     */
    public function container($container = 'default')
    {
        if (!isset($this->containers[$container])) {
            $this->containers[$container] = new Asset($container, $this->dispatcher);
        }

        return $this->containers[$container];
    }

    /**
     * Magic Method for calling methods on the default container.
     *
     * <code>
     *     // Call the "styles" method on the default container
     *     echo Antares\Asset::styles();
     *
     *     // Call the "add" method on the default container
     *     Antares\Asset::add('jquery', 'js/jquery.js');
     * </code>
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->container(), $method], $parameters);
    }

    /**
     * add scripts from params
     * 
     * @param array $params
     * @return \Antares\Asset\Factory
     */
    public function scriptsByParams(array $params = array())
    {

        if (!isset($params['resources']) or ! isset($params['position'])) {
            return $this;
        }
        if (empty($params['resources'])) {
            return $this;
        }
        $container = $this->container($params['position']);
        foreach ($params['resources'] as $name => $path) {
            $container->add($name, $path);
        }
        return $this;
    }

}

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
 namespace Antares\Config\Traits;

use Antares\Config\LoaderInterface;

trait CascadingTrait
{
    /**
     * The loader implementation.
     *
     * @var \Antares\Config\LoaderInterface
     */
    protected $loader;

    /**
     * The current environment.
     *
     * @var string
     */
    protected $environment;

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     *
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        $this->loader->addNamespace($namespace, $hint);
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->loader->getNamespaces();
    }

    /**
     * Get the loader implementation.
     *
     * @return \Illuminate\Config\LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Set the loader implementation.
     *
     * @param  \Antares\Config\LoaderInterface  $loader
     *
     * @return void
     */
    public function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get the current configuration environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }
}

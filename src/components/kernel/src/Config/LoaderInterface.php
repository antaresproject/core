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
 namespace Antares\Config;

interface LoaderInterface
{
    /**
     * Load the given configuration group.
     *
     * @param  string  $environment
     * @param  string  $group
     * @param  string  $namespace
     *
     * @return array
     */
    public function load($environment, $group, $namespace = null);

    /**
     * Determine if the given configuration group exists.
     *
     * @param  string  $group
     * @param  string  $namespace
     *
     * @return bool
     */
    public function exists($group, $namespace = null);

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     *
     * @return void
     */
    public function addNamespace($namespace, $hint);

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces();

    /**
     * Apply any cascades to an array of package options.
     *
     * @param  string  $environment
     * @param  string  $package
     * @param  string  $group
     * @param  array   $items
     *
     * @return array
     */
    public function cascadePackage($environment, $package, $group, $items);
}

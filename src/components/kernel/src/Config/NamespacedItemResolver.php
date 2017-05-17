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

use Illuminate\Support\NamespacedItemResolver as Resolver;

abstract class NamespacedItemResolver extends Resolver
{
    /**
     * Parse a key into namespace, group, and item.
     *
     * @param  string  $key
     *
     * @return array
     */
    public function parseKey($key)
    {
                                if (isset($this->parsed[$key])) {
            return $this->parsed[$key];
        }

                                if (strpos($key, '::') === false) {
            $segments = explode('.', $key);

            $parsed = $this->parseCustomSegments($segments);
        } else {
            $parsed = $this->parseNamespacedSegments($key);
        }

                                return $this->parsed[$key] = $parsed;
    }

    /**
     * Parse an array of basic segments.
     *
     * @param  array  $segments
     *
     * @return array
     */
    protected function parseCustomSegments(array $segments)
    {
        if (count($segments) >= 2) {
            $group = "{$segments[0]}/{$segments[1]}";

            if ($this->getLoader()->exists($group)) {
                return [null, $group, implode('.', array_slice($segments, 2))];
            }
        }

        return $this->parseBasicSegments($segments);
    }

    /**
     * Parse an array of namespaced segments.
     *
     * @param  string  $key
     *
     * @return array
     */
    protected function parseNamespacedSegments($key)
    {
        list($namespace, $item) = explode('::', $key);

                                if (in_array($namespace, $this->packages)) {
            return $this->parsePackageSegments($key, $namespace, $item);
        }

        return parent::parseNamespacedSegments($key);
    }

    /**
     * Parse the segments of a package namespace.
     *
     * @param  string  $key
     * @param  string  $namespace
     * @param  string  $item
     *
     * @return array
     */
    protected function parsePackageSegments($key, $namespace, $item)
    {
        $itemSegments = explode('.', $item);

                                if (! $this->getLoader()->exists($itemSegments[0], $namespace)) {
            return [$namespace, 'config', $item];
        }

        return parent::parseNamespacedSegments($key);
    }

    /**
     * Get the loader implementation.
     *
     * @return \Illuminate\Config\LoaderInterface
     */
    abstract public function getLoader();
}

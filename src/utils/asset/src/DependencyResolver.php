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

use RuntimeException;

class DependencyResolver
{
    /**
     * Sort and retrieve assets based on their dependencies.
     *
     * @param  array  $assets
     *
     * @return array
     */
    public function arrange($assets)
    {
        list($original, $sorted) = [$assets, []];

        $this->replaceAssetDependencies($assets);

        while (count($assets) > 0) {
            foreach ($assets as $asset => $value) {
                $this->evaluateAsset($asset, $value, $original, $sorted, $assets);
            }
        }

        return $sorted;
    }

    /**
     * Evaluate an asset and its dependencies.
     *
     * @param  string  $asset
     * @param  string  $value
     * @param  array   $original
     * @param  array   $sorted
     * @param  array   $assets
     *
     * @return void
     */
    protected function evaluateAsset($asset, $value, $original, &$sorted, &$assets)
    {
                                        if (count($assets[$asset]['dependencies']) == 0) {
            $sorted[$asset] = $value;

            unset($assets[$asset]);
        } else {
            $this->evaluateAssetWithDependencies($asset, $original, $sorted, $assets);
        }
    }

    /**
     * Evaluate an asset with dependencies.
     *
     * @param  string  $asset
     * @param  array   $original
     * @param  array   $sorted
     * @param  array   $assets
     *
     * @return void
     */
    protected function evaluateAssetWithDependencies($asset, $original, &$sorted, &$assets)
    {
        foreach ($assets[$asset]['dependencies'] as $key => $dependency) {
            if (! $this->dependencyIsValid($asset, $dependency, $original, $assets)) {
                unset($assets[$asset]['dependencies'][$key]);

                continue;
            }

                                                            if (isset($sorted[$dependency])) {
                unset($assets[$asset]['dependencies'][$key]);
            }
        }
    }

    /**
     * Verify that an asset's dependency is valid.
     *
     * A dependency is considered valid if it exists, is not a circular
     * reference, and is not a reference to the owning asset itself. If the
     * dependency doesn't exist, no error or warning will be given. For the
     * other cases, an exception is thrown.
     *
     * @param  string  $asset
     * @param  string  $dependency
     * @param  array   $original
     * @param  array   $assets
     *
     * @return bool
     *
     * @throws \RuntimeException
     */
    protected function dependencyIsValid($asset, $dependency, $original, $assets)
    {
                $isCircular = function ($asset, $dependency, $assets) {
            return isset($assets[$dependency]) && in_array($asset, $assets[$dependency]['dependencies']);
        };

        if (! isset($original[$dependency])) {
            return false;
        } elseif ($dependency === $asset) {
            throw new RuntimeException("Asset [$asset] is dependent on itself.");
        } elseif ($isCircular($asset, $dependency, $assets)) {
            throw new RuntimeException("Assets [$asset] and [$dependency] have a circular dependency.");
        }

        return true;
    }

    /**
     * Replace asset dependencies.
     *
     * @param  array  $assets
     *
     * @return void
     */
    protected function replaceAssetDependencies(&$assets)
    {
        foreach ($assets as $asset => $value) {
            if (empty($replaces = $value['replaces'])) {
                continue;
            }

            foreach ($replaces as $replace) {
                unset($assets[$replace]);
            }

            $this->resolveDependenciesForAsset($assets, $asset, $replaces);
        }
    }

    /**
     * Resolve asset dependencies after replacement.
     *
     * @param  array   $assets
     * @param  string  $asset
     * @param  array   $replaces
     *
     * @return array
     */
    protected function resolveDependenciesForAsset(&$assets, $asset, $replaces)
    {
        foreach ($assets as $name => $value) {
            $changed = false;

            foreach ($value['dependencies'] as $key => $dependency) {
                if (in_array($dependency, $replaces)) {
                    $changed = true;
                    unset($value['dependencies'][$key]);
                }
            }

            if ($changed) {
                $value['dependencies'][]       = $asset;
                $assets[$name]['dependencies'] = $value['dependencies'];
            }
        }

        $assets[$asset]['replaces'] = [];
    }
}

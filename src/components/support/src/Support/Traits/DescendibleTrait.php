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
 namespace Antares\Support\Traits;

trait DescendibleTrait
{
    /**
     * Get last descendant node from items recursively.
     *
     * @param  array   $array
     * @param  string  $key
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function descendants(array $array, $key = null)
    {
        if (is_null($key)) {
            return $array;
        }

        $keys  = explode('.', $key);
        $first = array_shift($keys);

        if (! isset($array[$first])) {
            return;
        }

        return $this->resolveLastDecendant($array[$first], $keys);
    }

    /**
     * Resolve last descendant node from items.
     *
     * @param  array  $array
     * @param  array  $keys
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function resolveLastDecendant($array, $keys)
    {
        $isLastDescendant = function ($array, $segment) {
            return (! is_array($array->childs) || ! isset($array->childs[$segment]));
        };

                                        foreach ($keys as $segment) {
            if ($isLastDescendant($array, $segment)) {
                return $array;
            }

            $array = $array->childs[$segment];
        }

        return $array;
    }
}

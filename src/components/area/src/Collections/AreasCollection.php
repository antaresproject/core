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

namespace Antares\Area\Middleware;

use Antares\Area\Contracts\AreaContract;
use ArrayAccess;
use Countable;

class AreasCollection implements ArrayAccess, Countable
{

    /**
     * @var AreaContract[]
     */
    protected $areas = [];

    /**
     * @param AreaContract $area
     */
    public function add(AreaContract $area)
    {
        $this->areas[$area->getId()] = $area;
    }

    /**
     * @return AreaContract[]
     */
    public function all(): array
    {
        return array_values($this->areas);
    }

    /**
     * @param AreaContract $area
     * @return bool
     */
    public function has(AreaContract $area): bool
    {
        return array_key_exists($area->getId(), $this->areas);
    }

    /**
     * @param string $id
     * @return AreaContract|null
     */
    public function getById(string $id)
    {
        if (array_key_exists($id, $this->areas)) {
            return $this->areas[$id];
        }
        return null;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->areas[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return isset($this->areas[$offset]) ? $this->areas[$offset] : null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if ($value instanceof AreaContract) {
            $this->add($value);
        }

        throw new \InvalidArgumentException('The given value has invalid type.');
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->areas[$offset]);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->areas);
    }

}

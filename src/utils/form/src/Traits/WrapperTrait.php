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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Traits;

/**
 * @author Marcin Domański <marcin@domanskim.pl>
 * Date: 27.03.17
 * Time: 13:21
 */
trait WrapperTrait
{

    /** @var array */
    public $wrapper;

    /**
     * @param bool $name
     * @return bool
     */
    public function hasWrapper($name = false): bool
    {
        return ($name) ? !empty($this->wrapper[$name]) : !empty($this->wrapper);
    }

    /**
     * @return array
     */
    public function getWrapper(): array
    {
        return $this->wrapper;
    }

    /**
     * If wrapper not empty - creates div with specified here attributes
     *
     * @param array $wrapper
     * @return self
     */
    public function setWrapper(array $wrapper)
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * @param $name
     * @param $value
     */
    public function addWrapper($name, $value)
    {
        if ($this->hasWrapper($name)) {
            $this->wrapper[$name] .= ' ' . $value;
        } else {
            $this->wrapper[$name] = $value;
        }
    }

}
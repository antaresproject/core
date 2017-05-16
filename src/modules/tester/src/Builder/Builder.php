<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Tester\Builder;

abstract class Builder
{

    /**
     * key - feature of abstract builder design pattern
     * 
     * @param String $name
     * @param array $attributes
     * @param Closure $callback
     */
    abstract public function build($name = null, array $attributes = [], $callback = null);
}

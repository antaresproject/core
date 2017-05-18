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

namespace Antares\Form\Contracts;


/**
 * @author Marcin Domański <marcin@domanskim.pl>
 * Date: 27.03.17
 * Time: 13:22
 */
interface Wrapperable
{

    /**
     * Check if wrapper is set or whether contains attribute with $name
     *
     * @param bool $name
     * @return mixed
     */
    public function hasWrapper($name = false);

    /**
     * Get wrapper attributes as array
     *
     * @return array
     */
    public function getWrapper();

    /**
     * Set prepared wrapper attributes
     *
     * @param array $wrapper
     * @return mixed
     */
    public function setWrapper(array $wrapper);

    /**
     * Add attribute to wrapper
     *
     * @param $name
     * @param $value
     * @return mixed
     */
    public function addWrapper($name, $value);
}
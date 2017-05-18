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
 * Time: 13:25
 */
interface Attributable
{

    /**
     * @param string $name
     * @return mixed
     */
    public function hasAttribute(string $name);

    /**
     * @param string $name
     * @param        $value
     * @return mixed
     */
    public function setAttribute(string $name, $value);

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function setAttributeIfNotExists($name, $value);

    /**
     * @param array $values
     * @return mixed
     */
    public function setAttributes(array $values);

    /**
     * @param string $name
     * @param null   $fallbackValue
     * @return mixed
     */
    public function getAttribute(string $name, $fallbackValue = null);

    /**
     * @return mixed
     */
    public function getAttributes();

}
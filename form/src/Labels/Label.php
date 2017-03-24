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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Labels;

/**
 * @author Marcin Domański <marcin@domanskim.pl>
 * Date: 24.03.17
 * Time: 14:06
 */
class Label extends AbstractLabel
{


    /**
     * Label constructor.
     *
     * @param string $name
     * @param array  $attributes
     */
    public function __construct(string $name, array $attributes = [])
    {
        $this->name = $name;
        $this->setAttributes($attributes);
    }

    protected function render()
    {
        // TODO: Implement render() method.
    }

}
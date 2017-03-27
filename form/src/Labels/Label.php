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

    public $type = 'default';

    protected $wrapperClass;

    /**
     * @return bool
     */
    public function hasWrapperClass(): bool
    {
        return !empty($this->wrapper);
    }

    /**
     * @return string
     */
    public function getWrapperClass()
    {
        return $this->wrapper;
    }

    /**
     * @param string $wrapperClass
     * @return self
     */
    public function setWrapper(string $wrapperClass): self
    {
        $this->wrapperClass = $wrapperClass;

        return $this;
    }

}
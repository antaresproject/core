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
 namespace Antares\Support;

class Expression
{
    /**
     * The value of the expression.
     *
     * @var string
     */
    protected $value;

    /**
     * Create a new expression instance.
     *
     * @param  string  $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get the string value of the expression.
     *
     * @return string
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Get the string value of the expression.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->get();
    }
}

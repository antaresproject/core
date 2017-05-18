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
 namespace Antares\Contracts\Html\Form;

interface Field
{
    /**
     * Get value of column.
     *
     * @param  mixed  $row
     * @param  mixed  $control
     * @param  array  $templates
     *
     * @return string
     */
    public function getField($row, $control, array $templates = []);
}

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

use Illuminate\Support\Fluent;

interface Control
{
    /**
     * Get templates.
     *
     * @return array
     */
    public function getTemplates();

    /**
     * Set templates.
     *
     * @param  array  $templates
     *
     * @return $this
     */
    public function setTemplates(array $templates = []);

    /**
     * Generate Field.
     *
     * @param  string  $type
     *
     * @return \Closure
     */
    public function generate($type);

    /**
     * Build field by type.
     *
     * @param  string  $type
     * @param  mixed  $row
     * @param  \Illuminate\Support\Fluent  $control
     *
     * @return \Illuminate\Support\Fluent
     */
    public function buildFieldByType($type, $row, Fluent $control);

    /**
     * Build data.
     *
     * @param  string  $type
     * @param  mixed  $row
     * @param  \Illuminate\Support\Fluent  $control
     *
     * @return \Illuminate\Support\Fluent
     */
    public function buildFluentData($type, $row, Fluent $control);

    /**
     * Render the field.
     *
     * @param  array  $templates
     * @param  \Illuminate\Support\Fluent  $data
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function render($templates, Fluent $data);
}

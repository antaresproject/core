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

interface Template
{
    /**
     * Button template.
     *
     * @param  \Antares\Contracts\Html\Form\Field  $field
     *
     * @return string
     */
    public function button(Field $field);

    /**
     * Checkbox template.
     *
     * @param  \Antares\Contracts\Html\Form\Field  $field
     *
     * @return string
     */
    public function checkbox(Field $field);

    /**
     * Checkboxes template.
     *
     * @param  \Antares\Contracts\Html\Form\Field  $field
     *
     * @return string
     */
    public function checkboxes(Field $field);

    /**
     * File template.
     *
     * @param  \Antares\Contracts\Html\Form\Field  $field
     *
     * @return string
     */
    public function file(Field $field);

    /**
     * Input template.
     *
     * @param  \Antares\Contracts\Html\Form\Field  $field
     *
     * @return string
     */
    public function input(Field $field);

    /**
     * Password template.
     *
     * @param  \Antares\Contracts\Html\Form\Field  $field
     *
     * @return string
     */
    public function password(Field $field);

    /**
     * Radio template.
     *
     * @param  \Antares\Contracts\Html\Form\Field  $field
     *
     * @return string
     */
    public function radio(Field $field);

    /**
     * Select template.
     *
     * @param  \Antares\Contracts\Html\Form\Field  $field
     *
     * @return string
     */
    public function select(Field $field);

    /**
     * Textarea template.
     *
     * @param  \Antares\Contracts\Html\Form\Field  $field
     *
     * @return string
     */
    public function textarea(Field $field);
}

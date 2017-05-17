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

use Closure;

interface Fieldset
{

    /**
     * Append a new control to the form.
     *
     * <code>
     *      // add a new control using just field name
     *      $fieldset->control('input:text', 'username');
     *
     *      // add a new control using a label (header title) and field name
     *      $fieldset->control('input:email', 'E-mail Address', 'email');
     *
     *      // add a new control by using a field name and closure
     *      $fieldset->control('input:text', 'fullname', function ($control)
     *      {
     *          $control->label = 'User Name';
     *
     *          // this would output a read-only output instead of form.
     *          $control->field = function ($row) {
     *              return $row->first_name.' '.$row->last_name;
     *          };
     *      });
     * </code>
     *
     * @param  string  $type
     * @param  mixed   $name
     * @param  mixed   $callback
     *
     * @return \Antares\Contracts\Html\Form\Field
     */
    public function control($type, $name, $callback = null);

    /**
     * Set Fieldset Legend name.
     *
     * <code>
     *     $fieldset->legend('User Information');
     * </code>
     *
     * @param  string  $name
     *
     * @return mixed
     */
    public function legend($name = null);

    /**
     * Get fieldset name.
     *
     * @return string
     */
    public function getName();

    /**
     * Updates the Fieldset closure.
     *
     * @param Closure|null $callback
     */
    public function update(Closure $callback = null);
}

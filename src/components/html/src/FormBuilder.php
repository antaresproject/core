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


namespace Antares\Html;

use Antares\Html\Support\FormBuilder as BaseFormBuilder;

class FormBuilder extends BaseFormBuilder
{

    /**
     * Create a checkboxes input field.
     *
     * @param  string  $name
     * @param  array  $list
     * @param  bool|array  $checked
     * @param  array  $options
     * @param  string  $separator
     *
     * @return string
     */
    public function checkboxes($name, array $list = [], $checked = null, array $options = [], $separator = '<br>')
    {
        $group = [];
        $name  = str_replace('[]', '', $name);

        foreach ($list as $id => $label) {
            $group[] = $this->generateCheckboxByGroup($id, $label, $name, $checked, $options);
        }

        return implode($separator, $group);
    }

    /**
     * Generate checkbox by group.
     *
     * @param  string  $id
     * @param  string  $label
     * @param  string  $name
     * @param  bool|array  $checked
     * @param  array  $options
     *
     * @return array
     */
    protected function generateCheckboxByGroup($id, $label, $name, $checked, array $options)
    {
        $identifier = sprintf('%s_%s', $name, $id);
        $key        = sprintf('%s[]', $name);
        $active     = in_array($id, (array) $checked);

        $options['id'] = $identifier;
        $control       = $this->checkbox($key, $id, $active, $options);
        $label         = $this->label($identifier, $label);

        return implode(' ', [$control, $label]);
    }

}

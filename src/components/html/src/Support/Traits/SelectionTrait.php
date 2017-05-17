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

namespace Antares\Html\Support\Traits;

use Closure;

trait SelectionTrait
{

    /**
     * Create a select box field.
     *
     * @param  string  $name
     * @param  array   $list
     * @param  string  $selected
     * @param  mixed   $options
     * @param  array   $optionsData
     *
     * @return string
     */
    public function select($name, $list = [], $selected = null, $options = [], $optionsData = [])
    {
        $selected        = $this->getValueAttribute($name, $selected);
        $options['id']   = $this->getIdAttribute($name, $options);
        !isset($options['name']) && $options['name'] = $name;
        $html            = [];
        $optionsData     = ($optionsData instanceof Closure) ? call_user_func($optionsData) : $optionsData;
        foreach ($list as $value => $display) {
            $html[] = $this->getSelectOption($display, $value, $selected, $optionsData);
        }


        $options = $this->getHtmlBuilder()->attributes($options);
        $list    = implode('', $html);

        return "<select{$options}>{$list}</select>";
    }

    /**
     * Get the select option for the given value.
     *
     * @param  string  $display
     * @param  string  $value
     * @param  string  $selected
     * @param  array   $optionsData
     *
     * @return string
     */
    public function getSelectOption($display, $value, $selected, array $optionsData = [])
    {
        if (is_array($display)) {
            return $this->optionGroup($display, $value, $selected, $optionsData);
        }
        return $this->option($display, $value, $selected, $optionsData);
    }

    /**
     * Create an option group form element.
     *
     * @param  array   $list
     * @param  string  $label
     * @param  string  $selected
     * @param  array   $optionsData
     *
     * @return string
     */
    protected function optionGroup($list, $label, $selected, array $optionsData = [])
    {
        $html = [];

        foreach ($list as $value => $display) {
            $html[] = $this->option($display, $value, $selected, $optionsData);
        }

        return '<optgroup label="' . e($label) . '">' . implode('', $html) . '</optgroup>';
    }

    /**
     * Create a select element option.
     *
     * @param  string  $display
     * @param  string  $value
     * @param  string  $selected
     * @param  array   $optionsData
     *
     * @return string
     */
    protected function option($display, $value, $selected, array $optionsData = [])
    {

        $selected = $this->getSelectedValue($value, $selected);
        $options  = ['value' => e($value), 'selected' => $selected];
        $data     = array_get($optionsData, $value, []);



        foreach ($data as $name => $value) {
            $options['data-' . str_slug($name)] = e($value);
        }


        return '<option' . $this->getHtmlBuilder()->attributes($options) . '>' . e($display) . '</option>';
    }

    /**
     * Determine if the value is selected.
     *
     * @param  string  $value
     * @param  string  $selected
     *
     * @return string
     */
    protected function getSelectedValue($value, $selected)
    {
        if (is_array($selected)) {
            return in_array($value, $selected) ? 'selected' : null;
        }

        return ((string) $value == (string) $selected) ? 'selected' : null;
    }

    /**
     * Create a select range field.
     *
     * @param  string  $name
     * @param  string  $begin
     * @param  string  $end
     * @param  string  $selected
     * @param  array   $options
     *
     * @return string
     */
    public function selectRange($name, $begin, $end, $selected = null, $options = [])
    {
        $range = array_combine($range = range($begin, $end), $range);

        return $this->select($name, $range, $selected, $options);
    }

    /**
     * Create a select year field.
     *
     * @param  string  $name
     * @param  string  $begin
     * @param  string  $end
     * @param  string  $selected
     * @param  array   $options
     *
     * @return string
     */
    public function selectYear($name, $begin, $end, $selected = null, $options = [])
    {
        return call_user_func([$this, 'selectRange'], $name, $begin, $end, $selected, $options);
    }

    /**
     * Create a select month field.
     *
     * @param  string  $name
     * @param  string  $selected
     * @param  array   $options
     * @param  string  $format
     *
     * @return string
     */
    public function selectMonth($name, $selected = null, $options = [], $format = '%B')
    {
        $months = [];

        foreach (range(1, 12) as $month) {
            $months[$month] = strftime($format, mktime(0, 0, 0, $month, 1));
        }

        return $this->select($name, $months, $selected, $options);
    }

    /**
     * Get html builder.
     *
     * @return \Antares\Html\Support\HtmlBuilder
     */
    abstract public function getHtmlBuilder();

    /**
     * Get the ID attribute for a field name.
     *
     * @param  string  $name
     * @param  array   $attributes
     *
     * @return string
     */
    abstract public function getIdAttribute($name, $attributes);

    /**
     * Get the value that should be assigned to the field.
     *
     * @param  string  $name
     * @param  string  $value
     *
     * @return string
     */
    abstract public function getValueAttribute($name, $value = null);
}

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


namespace Antares\Html\Form;

use Illuminate\Support\Fluent;
use Illuminate\Contracts\Support\Renderable;
use Antares\Contracts\Html\Form\Field as FieldContract;

class Field extends Fluent implements FieldContract
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
    public function getField($row, $control, array $templates = [])
    {
        $value = call_user_func($this->attributes['field'], $row, $control, $templates);
        if ($value instanceof Renderable) {
            return $value->render();
        }


        return $value;
    }

    /**
     * force edit field
     * 
     * @return \Antares\Html\Form\Field
     */
    public function forceEditable()
    {
        array_set($this->attributes, 'force_editable', true);
        array_set($this->attributes, 'force_displayable', true);
        return $this;
    }

    /**
     * force field can be displayed
     * 
     * @return \Antares\Html\Form\Field
     */
    public function forceDisplayable()
    {
        array_set($this->attributes, 'force_displayable', true);
        return $this;
    }

    /**
     * set wrapper params
     * 
     * @param array $wrapperAttrs
     * @return \Antares\Html\Form\Field
     */
    public function wrapper(array $wrapperAttrs = [])
    {
        $this->wrapper = $wrapperAttrs;
        return $this;
    }

    /**
     * Sets block attributes
     * 
     * @param array $blockAttrs
     * @return \Antares\Html\Form\Field
     */
    public function block(array $blockAttrs = [])
    {
        $this->block = array_merge(array_get($this->attributes, 'block', []), $blockAttrs);
        return $this;
    }

    /**
     * Gets block attributes
     * 
     * @param String $key
     * @param mixed $default
     * @return String
     */
    public function getBlock($key, $default = null)
    {
        if (!isset($this->attributes['block'])) {
            return $default;
        }
        if (!is_null($key) && !array_key_exists($key, $this->attributes['block'])) {
            return $default;
        }
        if (is_null($key)) {
            return $this->attributes['block'];
        }
        if (!is_null($key) && array_key_exists($key, $this->attributes['block'])) {
            return $this->attributes['block'][$key];
        }
    }

    /**
     * set wrapper params
     * 
     * @param array $labelWrapperAttrs
     * @return \Antares\Html\Form\Field
     */
    public function labelWrapper(array $labelWrapperAttrs = [])
    {
        $this->labelWrapper = $labelWrapperAttrs;
        return $this;
    }

    /**
     * get wrapper attributes
     * 
     * @return array|mixed
     */
    public function getWrapper($key = null, $default = null)
    {
        if (!isset($this->attributes['wrapper'])) {
            return $default;
        }
        if (!is_null($key) && !array_key_exists($key, $this->attributes['wrapper'])) {
            return $default;
        }
        if (is_null($key)) {
            return $this->attributes['wrapper'];
        }
        if (!is_null($key) && array_key_exists($key, $this->attributes['wrapper'])) {
            return $this->attributes['wrapper'][$key];
        }
    }

    /**
     * get wrapper attributes
     * 
     * @return array|mixed
     */
    public function getLabelWrapper($key = null, $default = null)
    {
        if (!isset($this->attributes['labelWrapper'])) {
            return $default;
        }
        if (!is_null($key) && !array_key_exists($key, $this->attributes['labelWrapper'])) {
            return $default;
        }
        if (is_null($key)) {
            return $this->attributes['labelWrapper'];
        }
        if (!is_null($key) && array_key_exists($key, $this->attributes['labelWrapper'])) {
            return $this->attributes['labelWrapper'][$key];
        }
    }

    /**
     * Whether field has wrapper container
     * 
     * @return boolean
     */
    public function hasWrapper()
    {
        return !empty($this->attributes['wrapper']);
    }

    /**
     * Whether field has label
     * 
     * @return boolean
     */
    public function hasLabel()
    {
        return strlen(array_get($this->attributes, 'label')) > 0;
    }

    /**
     * Whether field has named attribute
     * 
     * @return boolean
     */
    public function has($name)
    {
        return strlen(array_get($this->attributes, $name)) > 0;
    }

    public function getOptions()
    {
        $options = array_get($this->attributes, 'options', []);
        if (empty($options)) {
            return [];
        }
        return $options instanceof \Closure ? call_user_func($options) : $options;
    }

    public function hasOptions()
    {
        return !empty(array_get($this->attributes, 'options', []));
    }

}

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


namespace Antares\Html\Adapter;

use Antares\Contracts\Html\Adapter\FieldPermissionAdapter as FieldPermissionContract;
use Antares\Contracts\Html\Form\Field as FieldContract;
use Antares\Support\Facades\Memory;
use Antares\Support\Collection;

class FieldPermissionAdapter implements FieldPermissionContract
{

    /**
     * key memory seperator
     */
    const separator = '::';

    /**
     * memory provider
     *
     * @var \Antares\Memory\Provider
     */
    protected $runtime;

    /**
     * create instance of adapter
     */
    public function __construct()
    {
        $this->runtime = Memory::make('runtime');
    }

    /**
     * is frontend
     * 
     * @param String $componentName
     * @return boolean
     */
    private function isFrontend($componentName = null)
    {
        return (!is_null($componentName) && $componentName == 'app');
    }

    /**
     * form config keyname getter
     * 
     * @param String $name
     * @param array $control
     * @return String
     */
    private function getFormConfigKey($name, array $control)
    {
        if ($this->isFrontend($control['component'])) {
            $control['component'] = 'content';
            $control['method']    = 'index';
        }
        return implode(self::separator, [$control['component'], $control['action'], $control['method'], $name]);
    }

    /**
     * fieldset resolver
     * 
     * @param Collection $fieldsets
     * @return boolean|Collection
     */
    public function resolveFields(Collection $fieldsets, $key = null)
    {
        $name          = $fieldsets->first()->name;
        $name instanceof \Closure and $name          = sprintf('fieldset-%d', $fieldsets->count());
        $formName      = str_slug($name);
        if (!app('antares.installed') or ! ($configuration = $this->validate($formName, $key)) !== false) {
            return false;
        }
        $editable    = array_get($configuration, 'editable', []);
        $displayable = array_get($configuration, 'displayable', []);
        /** continue recreate form configuration * */
        /** form can have more than one fieldset spec * */
        $return      = [];
        foreach ($fieldsets as $fieldsetCopy) {
            array_push($return, $fieldsetCopy);
        }

        $clone = $fieldsets;
        foreach ($clone as $fieldset) {
            if (empty($fieldset->controls)) {
                return;
            }
            $controls = [];
            /** for each control in fieldset * */
            foreach ($fieldset->controls as $control) {
                if (!$this->display($control, $displayable)) {
                    continue;
                }
                $control = $this->resolveEditableField($control, $editable);
                array_push($controls, $control);
            }
            $fieldset->setControls($controls);
        }
        return $clone;
    }

    /**
     * resolve field display depends on form configuration
     * 
     * @param FieldContract $control
     * @param array $displayable
     */
    protected function display(FieldContract $control, array $displayable = array())
    {
        $name              = $control->get('name');
        $shouldBeDisplayed = false;
        if ($control->force_displayable) {
            return true;
        }
        foreach ($displayable as $field) {

            if ($name == $field['name']) {
                $shouldBeDisplayed = true;
                break;
            }
        }
        return $shouldBeDisplayed;
    }

    /**
     * resolve field edition depends on form configuration
     * 
     * @param FieldContract $control
     * @param array $editable
     * @param array $disabled
     */
    protected function resolveEditableField(FieldContract $control, array $editable = array())
    {
        $name = $control->get('name');
        if ($control->force_editable) {
            return $control;
        }
        $shouldBeEdited = false;
        foreach ($editable as $editableField) {
            if ($name == $editableField['name']) {
                $shouldBeEdited = true;
                break;
            }
        }
        $disable    = $shouldBeEdited ? [] : ['disabled' => 'disabled', 'readonly' => 'readonly'];
        $attributes = array_merge($control->get('attributes'), $disable);
        $control->attributes($attributes);
        return $control;
    }

    /**
     * validate current form configuration and matching with route middleware settings
     * 
     * @param String $name
     * @param String $key
     * @return boolean|array
     */
    protected function validate($name, $key = null)
    {
        if (!is_null($key)) {
            return Memory::make('forms-config')->get($key);
        }
        $control = $this->runtime->get('control', []);

        /** before middleware gives information about current middleware action and controller * */
        if (empty($control)) {
            return false;
        }
        $keyname       = $this->getFormConfigKey($name, $control);
        /** we will get this from cache * */
        $configuration = Memory::make('forms-config')->get($keyname);
        /** when we dont find any information about form in current route * */
        if (empty($configuration)) {
            return false;
        }
        /** when current action is not same with action from form configuration * */
        if ((int) $control['action_id'] !== (int) $configuration['action']) {
            return false;
        }
        return $configuration;
    }

    /**
     * runtime memory getter
     * 
     * @return \Antares\Contracts\Memory\Provider
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

}

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

namespace Antares\Customfield\Traits;

trait Customfields
{

    /**
     * Gets assigned customfieldsa
     * 
     * @return array
     */
    protected function getAssignedCustomfields()
    {

        $customfields = array_get(app('customfields')->get(), get_called_class(), []);
        return !is_array($customfields) ? [$customfields] : $customfields;
    }

    /**
     * Whether model has customfield
     * 
     * @param String $name
     * @return boolean
     */
    public static function hasCustomfield($name = null)
    {

        $customfields = $this->getAssignedCustomfields();
        if (empty($customfields)) {
            return false;
        }
        foreach ($customfields as $customfield) {
            if ($customfield->getName() !== $name) {
                continue;
            }
            return true;
        }
        return false;
    }

    /**
     * Whether model has customfield
     * 
     * @param String $name
     * @return boolean
     */
    public function customfield($name = null, $default = null)
    {
        $customfields = $this->getAssignedCustomfields();
        foreach ($customfields as $customfield) {
            if ($customfield->getName() === $name) {
                $value = $customfield->setModel($this)->getValue();
                return is_null($value) ? $default : $value;
            }
        }
        return $default;
    }

    /**
     * Gets all customfields available in model
     * 
     * @param String $fieldset
     * @return array
     */
    public function customfields($fieldset = null)
    {
        $return       = [];
        $customfields = $this->getAssignedCustomfields();

        foreach ($customfields as $customfield) {
            if (is_null($customfield)) {
                continue;
            }
            $value = $customfield->setModel($this)->getValue();
            array_set($return, $customfield->getName(), $value);
        }
        return $return;
    }

    /**
     * 
     * @return type
     */
    function getCustomfieldsAttribute()
    {
        return $this->customfields();
    }

}

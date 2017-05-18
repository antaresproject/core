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

class CustomfieldsFinder
{

    /**
     * Customfields container
     *
     * @var array 
     */
    protected $customfields = [];

    /**
     * List of configurable customfields
     *
     * @var array 
     */
    protected $configurable = [];

    /**
     * Construct
     */
    public function __construct()
    {
        if (empty($this->customfields)) {
            $this->customfields = $this->getCustomfields();
        }
    }

    /**
     * Gets customfields container
     * 
     * @return array
     */
    public function get()
    {
        return $this->customfields;
    }

    /**
     * Gets extensions with customfields
     * 
     * @return array
     */
    protected function getCustomfields()
    {
        $extensions = extensions();
        $return     = [];
        event('customfields.before.search', $return);
        foreach ($extensions as $name => $extension) {
            $name   = 'antares/' . str_replace(['component-', 'module-'], '', $extension['name']);
            $config = config($name . '::customfields');
            if (empty($config)) {
                continue;
            }
            if (!$this->validate($config)) {
                continue;
            }
            array_walk($config, function(&$value, $key) {
                if (is_array($value)) {
                    foreach ($value as $index => &$customfield) {
                        if (!class_exists($customfield)) {
                            unset($value[$index]);
                            continue;
                        }
                        $customfield = app($customfield);
                    }
                } else {
                    $value = app($value);
                }
            });

            $return = array_merge($return, $config);
        }
        event('customfields.after.search', [$return]);
        return $return;
    }

    /**
     * Whether customfield configuration is valid
     * 
     * @param array $config
     * @return boolean
     */
    private function validate(array $config = [])
    {
        $customfields = [];
        foreach ($config as $classname => $customfield) {
            if (!class_exists($classname)) {
                continue;
            }
            array_push($customfields, $customfield);
        }
        return !empty($customfields);
    }

    /**
     * Gets configurable customfields
     * 
     * @param String $group
     * @return array
     */
    public function getConfigurable($group = null)
    {
        if (empty($this->configurable)) {
            foreach ($this->customfields as $category => $customfield) {
                foreach ($customfield as $field) {
                    if (!$field->configurable()) {
                        continue;
                    }
                    $this->configurable[strtolower(last(explode('\\', $category)))][] = $field->getName();
                }
            }
        }
        return !is_null($group) ? array_get($this->configurable, $group, []) : $this->configurable;
    }

}

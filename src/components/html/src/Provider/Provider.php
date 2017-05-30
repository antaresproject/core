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


namespace Antares\Html\Provider;

use Antares\Memory\Provider as BaseProvider;

class Provider extends BaseProvider
{

    /**
     * get ids based on key map
     * 
     * @return type
     */
    public function ids()
    {
        $keyMap = $this->getHandler()->getKeyMap();
        $return = [];
        foreach ($keyMap as $name => $values) {
            $return = array_add($return, $name, $values['id']);
        }
        return $return;
    }

    /**
     * get single key instance
     * 
     * @param String $key
     * @param mixed $default
     * @return mixed
     */
    public function id($key = null, $default = null)
    {
        $element = $this->getHandler()->getKeyMapElement($key, $default);
        return is_array($element) && array_key_exists('id', $element) ? (int) $element['id'] : $default;
    }

    /**
     * gets name by id
     * 
     * @param numeric $id
     * @param mixed $default
     * @return mixed
     */
    public function getNameById($id, $default = null)
    {
        $keyMap = $this->getHandler()->getKeyMap();
        if (empty($keyMap)) {
            return $default;
        }
        foreach ($keyMap as $name => $value) {
            if (isset($value['id']) && $value['id'] == $id) {
                return $name;
            }
        }
        return $default;
    }

}

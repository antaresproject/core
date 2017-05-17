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


namespace Antares\Area\Model;

use Antares\Area\Contracts\AreaContract;

class Area implements AreaContract {
    
    /**
     *
     * @var string
     */
    protected $id;
    
    /**
     *
     * @var string
     */
    protected $label;
    
    /**
     * 
     * @param string $id
     * @param string $label
     */
    public function __construct($id, $label) {
        $this->id       = (string) $id;
        $this->label    = (string) $label;
    }
    
    /**
     * Return an area ID.
     * 
     * @return string
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Return a user-friendly name.
     * 
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Check if areas have the same ID.
     * 
     * @param AreaContract $area
     * @return boolean
     */
    public function isEquals(AreaContract $area) {
        return $this->getId() === $area->getId();
    }

    /**
     * 
     * @return string
     */
    public function __toString() {
        return $this->getLabel();
    }

    /**
     * 
     * @return string
     */
    public function getRouteKey() {
        return $this->getId();
    }

    /**
     * 
     * @return string
     */
    public function getRouteKeyName() {
        return 'area';
    }

}

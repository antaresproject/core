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


namespace Antares\Area\Contracts;

interface AreaManagerContract {
    
    /**
     * @return AreaContract
     */
    public function getCurrentArea();
    
    /**
     * 
     * @return boolean
     */
    public function isClientArea();
    
    /**
     * 
     * @return boolean
     */
    public function isAdminArea();
    
    /**
     * 
     * @return AreaContract[]
     */
    public function getAreas();
    
    /**
     * 
     * @param string $id
     * @return AreaContract
     */
    public function getById($id);
    
}

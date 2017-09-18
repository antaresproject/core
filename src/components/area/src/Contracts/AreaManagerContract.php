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

use Antares\Area\Middleware\AreasCollection;

interface AreaManagerContract {

    /**
     * Returns the default area.
     *
     * @return AreaContract
     */
    public function getDefault() : AreaContract;

    /**
     * Checks if the route has area.
     *
     * @return bool
     */
    public function hasAreaInUri() : bool;

    /**
     * Gets an area object based on the current authentication and URI..
     *
     * @return AreaContract
     */
    public function getCurrentArea() : AreaContract;

    /**
     * Returns collection of frontend areas.
     *
     * @return AreasCollection
     */
    public function getFrontendAreas() : AreasCollection;

    /**
     * Returns collection of backend areas.
     *
     * @return AreasCollection
     */
    public function getBackendAreas() : AreasCollection;

    /**
     * Checks if the current area belongs to the Frontend Areas.
     *
     * @return boolean
     */
    public function isFrontendArea() : bool;

    /**
     * Checks if the current area belongs to the Backend Areas.
     *
     * @return boolean
     */
    public function isBackendArea() : bool;

    /**
     * Returns a collection with areas.
     *
     * @return AreasCollection
     */
    public function getAreas() : AreasCollection;

    /**
     * Returns an area object based on ID. Null returns if not found.
     *
     * @param string $id
     * @return AreaContract | null
     */
    public function getById(string $id) : ?AreaContract;

    /**
     * Returns an area object based on ID. Default area returns if not found the desired one.
     *
     * @param string $id
     * @return AreaContract
     */
    public function getByIdOrDefault(string $id) : AreaContract;
    
}

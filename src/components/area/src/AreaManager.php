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


namespace Antares\Area;

use Antares\Area\Contracts\AreaManagerContract;
use Antares\Area\Contracts\AreaContract;
use Illuminate\Auth\AuthManager as Auth;
use Antares\Area\Model\Area;

class AreaManager implements AreaManagerContract
{

    /**
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Collection of available areas
     *
     * @var array
     */
    protected $areas = [];

    /**
     * Constructing
     * 
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
        $areas      = config('areas.areas', []);

        foreach ($areas as $name => $title) {
            array_set($this->areas, $name, new Area($name, trans($title)));
        }
    }

    /**
     * Get an area object based on the current authentication.
     * 
     * @return AreaContract
     */
    public function getCurrentArea()
    {
        return $this->isAdminArea() ? $this->areas[config('areas.default')] : key((array_except(config('areas.areas'), config('areas.default'))));
    }

    /**
     * Check if the current authentication belongs to the Client Area.
     * 
     * @return boolean
     */
    public function isClientArea()
    {
        return $this->auth->isAny(['member']);
    }

    /**
     * Check if the current authentication belongs to the Admin Area.
     * 
     * @return boolean
     */
    public function isAdminArea()
    {
        return !$this->isClientArea();
    }

    /**
     * Return an array with areas.
     * 
     * @return AreaContract[]
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * Return an area object based on ID. Null returns if not found.
     * 
     * @param string $id
     * @return AreaContract | null
     */
    public function getById($id)
    {
        foreach ($this->getAreas() as $area) {
            if ($area->getId() === (string) $id) {
                return $area;
            }
        }
    }

}

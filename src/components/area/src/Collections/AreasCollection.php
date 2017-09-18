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

namespace Antares\Area\Middleware;

use Antares\Area\Contracts\AreaContract;

class AreasCollection {

    /**
     * @var AreaContract[]
     */
    protected $areas = [];

    /**
     * @param AreaContract $area
     */
    public function add(AreaContract $area) {
        $this->areas[$area->getId()] = $area;
    }

    /**
     * @return AreaContract[]
     */
    public function all() : array {
        return array_values($this->areas);
    }

    /**
     * @param AreaContract $area
     * @return bool
     */
    public function has(AreaContract $area) : bool {
        return array_key_exists($area->getId(), $this->areas);
    }

    /**
     * @param string $id
     * @return AreaContract|null
     */
    public function getById(string $id) : ?AreaContract {
        if( array_key_exists($id, $this->areas) ) {
            return $this->areas[$id];
        }
        return null;
    }

}

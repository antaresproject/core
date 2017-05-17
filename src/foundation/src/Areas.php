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

namespace Antares\Foundation;

use Antares\Model\Role;

class Areas
{

    /**
     * Levels collection
     *
     * @var \Illuminate\Support\Collection
     */
    protected $areaRoles;

    /**
     * Available levels
     *
     * @var array
     */
    protected $areas = [];

    /**
     * Role model instance
     *
     * @var \Antares\Model\Role 
     */
    protected $model;

    /**
     * Constructing
     * 
     * @param Role $model
     */
    public function __construct(Role $model)
    {
        $this->model = $model;
        if (app_installed()) {
            $this->refresh();
        }
    }

    /**
     * Creates levels collection
     * 
     * @return \Antares\Foundation\Levels
     */
    public function refresh()
    {
        $collection      = $this->model->query()->get();
        $this->areaRoles = $collection;
        $filtered        = $collection->pluck('area')->unique()->filter(function($element) {
            return !is_null($element);
        });
        $this->areas = $filtered->toArray();
        return $this;
    }

    /**
     * Finds matched route prefix name as level name
     * 
     * @param String $name
     * @param mixed $default
     */
    public function findMatched($name, $default = null)
    {
        if (is_null($name)) {
            return $default;
        }
        if (!in_array($name, $this->areas)) {
            return $default;
        }
        return $name;
    }

    /**
     * Gets user valid level
     * 
     * @return String
     */
    public function getUserArea()
    {
        if (auth()->guest()) {
            return '';
        }
        $user      = auth()->user();
        $userRoles = $user->roles->pluck('id');
        $area      = null;
        $role      = null;
        foreach ($userRoles as $userRole) {
            $filtered = $this->areaRoles->where('id', $userRole);
            if ($filtered->count() > 0) {
                $model = $filtered->first();
                $role  = $model->name;
                $area  = $model->area;
                break;
            }
        }
        return !is_null($area) ? $area : config('antares/foundation::handles', 'antares');
    }

}

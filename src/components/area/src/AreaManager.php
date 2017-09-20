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
use Antares\Area\Middleware\AreasCollection;
use Antares\Model\User;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Antares\Area\Model\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AreaManager implements AreaManagerContract
{

    /**
     * Auth Guard instance
     *
     * @var \Illuminate\Contracts\Auth\Factory|\Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $auth;

    /**
     * Request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * Configuration array
     *
     * @var array
     */
    protected $config = [];

    /**
     * Collection of available areas
     *
     * @var AreaContract[]
     */
    protected $areas = [];

    /**
     * Default area instance
     *
     * @var AreaContract
     */
    protected $default;

    /**
     * AreaManager constructor.
     * @param Request $request
     * @param AuthFactory $auth
     * @param array $config
     */
    public function __construct(Request $request, AuthFactory $auth, array $config = [])
    {
        $this->auth    = $auth;
        $this->request = $request;
        $this->config  = $config;
        $this->areas   = new AreasCollection();

        $areas   = Arr::get($this->config, 'areas', []);
        $default = Arr::get($this->config, 'default', 'client');

        foreach ($areas as $name => $title) {
            $this->areas->add(new Area($name, trans($title)));
        }

        $this->default = $this->areas->getById($default);
    }

    /**
     * Returns the default area.
     *
     * @return AreaContract
     */
    public function getDefault(): AreaContract
    {
        return $this->default;
    }

    /**
     * Checks if the route has area.
     *
     * @return bool
     */
    public function hasAreaInUri(): bool
    {
        $segment = $this->request->segment(1);
        $area    = $segment ? $this->getById($segment) : null;

        return !!$area;
    }

    /**
     * Gets an area object based on the current authentication and URI.
     * 
     * @return AreaContract
     */
    public function getCurrentArea(): AreaContract
    {
        $segment = $this->request->segment(1);
        $area    = $segment ? $this->getById($segment) : null;

        if (!$area && $this->auth->check()) {
            /* @var $user User */
            $user = $this->auth->user();
            $area = $this->areas->getById($user->getArea());
        }

        return $area ?: $this->getDefault();
    }

    /**
     * Returns collection of frontend areas.
     *
     * @return AreasCollection
     */
    public function getFrontendAreas(): AreasCollection
    {
        $areas      = (array) Arr::get($this->config, 'routes.frontend', []);
        $collection = new AreasCollection();

        foreach ($areas as $areaId) {
            $area = $this->areas->getById($areaId);

            if ($area) {
                $collection->add($area);
            }
        }

        return $collection;
    }

    /**
     * Returns collection of backend areas.
     *
     * @return AreasCollection
     */
    public function getBackendAreas(): AreasCollection
    {
        $areas      = (array) Arr::get($this->config, 'routes.backend', []);
        $collection = new AreasCollection();

        foreach ($areas as $areaId) {
            $area = $this->areas->getById($areaId);

            if ($area) {
                $collection->add($area);
            }
        }

        return $collection;
    }

    /**
     * Checks if the current area belongs to the Frontend Areas.
     * 
     * @return boolean
     */
    public function isFrontendArea(): bool
    {
        return $this->getFrontendAreas()->has($this->getCurrentArea());
    }

    /**
     * Checks if the current area belongs to the Backend Areas.
     * 
     * @return boolean
     */
    public function isBackendArea(): bool
    {
        return $this->getBackendAreas()->has($this->getCurrentArea());
    }

    /**
     * Returns a collection with areas.
     * 
     * @return AreasCollection
     */
    public function getAreas(): AreasCollection
    {
        return $this->areas;
    }

    /**
     * Returns an area object based on ID. Null returns if not found.
     * 
     * @param string $id
     * @return AreaContract | null
     */
    public function getById(string $id)
    {
        return $this->areas->getById($id);
    }

    /**
     * Returns an area object based on ID. Default area returns if not found the desired one.
     *
     * @param string $id
     * @return AreaContract
     */
    public function getByIdOrDefault(string $id): AreaContract
    {
        $area = $this->getById($id);

        return $area ?: $this->getDefault();
    }

}

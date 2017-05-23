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

namespace Antares\Acl;

use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class Action
{

    /**
     * Route name.
     *
     * @var
     */
    protected $routeName;

    /**
     * Action name.
     *
     * @var
     */
    protected $action;

    /**
     * Construct
     * 
     * @param String $routeName
     * @param String $action
     */
    public function __construct($routeName, $action)
    {
        $this->routeName = $routeName;
        $this->action    = $action;
    }

    /**
     * Returns the route name (without area).
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * Returns the action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Returns the action as slug name.
     *
     * @return string
     */
    public function getActionAsParameter()
    {
        return Str::slug($this->action);
    }

    /**
     * Determines if the given route (and area) match to the action.
     *
     * @param Route $route
     * @param string|null $area
     * @return bool
     */
    public function isMatchToRoute(Route $route, string $area = null)
    {
        $areaPart = $area ? $area . '.' : '';

        return Str::is($areaPart . $this->getRouteName(), $route->getName());
    }

}

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

namespace Antares\Url\Traits;

use Antares\Acl\RoleActionList;
use Illuminate\Routing\RouteCollection;

trait RoutePermission
{

    /**
     * 
     * @param RouteCollection $routes
     * @param RoleActionList $roleActionList
     */
    public function bindPermissions(RouteCollection $routes, RoleActionList $roleActionList)
    {
        $actions = $roleActionList->getActions();

        foreach ($routes as $route) {
            $action = array_first($actions, function($action, $index) use($route) {
                return $action->isMatchToRoute($route);
            });

            if ($action) {
                $route->middleware('antares.can:' . $action->getActionAsParameter());
            }
        }
    }

}

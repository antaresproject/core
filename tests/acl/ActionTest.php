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


namespace Antares\Acl\Tests;

use Illuminate\Routing\Route;
use Antares\Acl\Action;

class ActionTest extends \PHPUnit_Framework_TestCase {
    
    public function testGetMethods() {
        $action = new Action('route.name', 'List Modules');
        
        $this->assertEquals('route.name', $action->getRouteName());
        $this->assertEquals('List Modules', $action->getAction());
        $this->assertEquals('list-modules', $action->getActionAsParameter());
    }
    
    public function testMatchingToRoute() {
        $route = new Route(['GET'], 'test/url', ['uses' => 'Controller@method']);
        $route->name('route.name');
        
        $action = new Action('route.name', 'List Modules');
        
        $this->assertTrue($action->isMatchToRoute($route));
    }

    public function testMatchingToRouteByWildcard() {
        $route = new Route(['GET'], 'test/url', ['uses' => 'Controller@method']);
        $route->name('route.name');

        $action = new Action('route.*', 'List Modules');

        $this->assertTrue($action->isMatchToRoute($route));
    }
    
}

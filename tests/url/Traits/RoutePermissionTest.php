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


namespace Antares\Url\Tests;

use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Route;
use Antares\Url\Traits\RoutePermission;
use Antares\Acl\RoleActionList;
use Antares\Acl\Action;

class RoutePermissionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var RoleActionList
     */
    protected $roleActionList;

    protected $traitObject;

    public function setUp() {
        parent::setUp();

        $this->roleActionList = new RoleActionList();

        $this->roleActionList->add('admin', [
            new Action('route.resource.index', 'List'),
            new Action('route.resource.create', 'Add Item'),
        ]);

        $this->traitObject = $this->getObjectForTrait(RoutePermission::class);
    }

    public function testCustomRoute() {
        $index  = (new Route(['GET'], 'resource/index', ['uses' => 'ResourceController@index']))->name('route.resource.index');
        $create = (new Route(['GET'], 'resource/create', ['uses' => 'ResourceController@create']))->name('route.resource.create');
        $edit   = (new Route(['GET'], 'resource/edit', ['uses' => 'ResourceController@edit']))->name('route.resource.edit');

        $routes = new RouteCollection;
        $routes->add($index);
        $routes->add($create);
        $routes->add($edit);

        $this->traitObject->bindPermissions($routes, $this->roleActionList);

        $this->assertContains('antares.can:list', $index->middleware());
        $this->assertContains('antares.can:add-item', $create->middleware());
        $this->assertCount(0, $edit->middleware());
    }

}

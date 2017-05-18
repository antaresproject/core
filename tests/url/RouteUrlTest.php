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

use Mockery as m;
use Illuminate\Routing\Route;
use Antares\Acl\RoleActionList;
use Antares\Acl\Action;
use Antares\Url\Permissions\CanHandler;
use Antares\Url\RouteUrl;

class RouteUrlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RoleActionList
     */
    protected $roleActionList;

    /**
     * @var Mockery
     */
    protected $canHandler;

    public function setUp()
    {
        parent::setUp();

        $this->roleActionList = new RoleActionList();

        $this->roleActionList->add('admin', [
            new Action('route.resource.index', 'List'),
            new Action('route.resource.create', 'Add Item'),
        ]);

        $this->canHandler = m::mock(CanHandler::class);
    }

    public function testNotAuthorizedWithoutAction()
    {
        $this->canHandler->shouldReceive('canAuthorize')->with(NULL)->once()->andReturn(false)->getMock();

        $route = (new Route(['GET'], 'resource/index', ['uses' => 'ResourceController@index']));
        $url   = new RouteUrl($this->canHandler, $route, 'Label');

        $this->assertFalse($url->isAuthorized());
    }

    public function testNotAuthorizedWithAction()
    {
        $this->canHandler->shouldReceive('canAuthorize')->with('list-items')->once()->andReturn(false)->getMock();

        $route = (new Route(['GET'], 'resource/index', ['uses' => 'ResourceController@index']))->middleware('antares.can:list-items');
        $url   = new RouteUrl($this->canHandler, $route, 'Label');

        $this->assertFalse($url->isAuthorized());
    }

    public function testAuthorizedWithAction()
    {
        $this->canHandler->shouldReceive('canAuthorize')->with('list-items')->once()->andReturn(true)->getMock();

        $route = (new Route(['GET'], 'resource/index', ['uses' => 'ResourceController@index']))->middleware('antares.can:list-items');
        $url   = new RouteUrl($this->canHandler, $route, 'Label');

        $this->assertTrue($url->isAuthorized());
    }

}

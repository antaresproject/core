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

namespace Antares\Area\Tests;

use Mockery as m;
use Antares\Testing\ApplicationTestCase;
use Antares\Area\Model\Area;
use Antares\Area\Contracts\AreaManagerContract;
use Antares\Area\Contracts\AreaContract;
use Antares\Area\AreaManager;

class AreaManagerTest extends ApplicationTestCase
{

    public function testContract()
    {
        $areaManager = new AreaManager($this->app->make('auth'));
        $this->assertInstanceOf(AreaManagerContract::class, $areaManager);
    }

    public function testAreas()
    {
        $areaManager = new AreaManager($this->app->make('auth'));
        $areas       = $areaManager->getAreas();
        $this->assertInternalType('array', $areas);
        $this->assertCount(2, $areas);
        $this->assertInstanceOf(AreaContract::class, head($areas));
    }

    public function testGetAreaById()
    {
        $clientArea  = new Area('administrators', 'Administrators');
        $adminArea   = new Area('users', 'Users');
        $areaManager = new AreaManager($this->app->make('auth'));
        $this->assertEquals($clientArea, $areaManager->getById('administrators'));
        $this->assertEquals($adminArea, $areaManager->getById('users'));
        $this->assertNull($areaManager->getById('dump'));
    }

    public function testAdminArea()
    {
        $this->markTestIncomplete('This test has not been implemented yet because of wrong isAny method implementation.');

        $this->auth->shouldReceive('isAny')->withArgs(['member'])->once()->andReturn(false);

        $adminArea   = new Area('admin', 'Admin Area');
        $areaManager = new AreaManager($this->auth);

        $this->assertTrue($areaManager->isAdminArea());
        $this->assertFalse($areaManager->isClientArea());
        $this->assertEquals($adminArea, $areaManager->getCurrentArea());
    }

    public function testClientArea()
    {
        $this->markTestIncomplete('This test has not been implemented yet because of wrong isAny method implementation.');

        $this->auth->shouldReceive('isAny')->withArgs(['member'])->once()->andReturn(true);

        $clientArea  = new Area('client', 'Client Area');
        $areaManager = new AreaManager($this->auth);

        $this->assertTrue($areaManager->isClientArea());
        $this->assertFalse($areaManager->isAdminArea());
        $this->assertEquals($clientArea, $areaManager->getCurrentArea());
    }

}

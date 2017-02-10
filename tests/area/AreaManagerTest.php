<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Area\Tests;

use Mockery as m;
use Antares\Testing\TestCase;
use Antares\Area\Model\Area;
use Antares\Area\Contracts\AreaManagerContract;
use Antares\Area\Contracts\AreaContract;
use Antares\Area\AreaManager;
use Antares\Auth\AuthManager as Auth;

class AreaManagerTest extends TestCase {
    
    /**
     *
     * @var Mockery
     */
    protected $auth;
    
    public function setUp() {
        parent::setUp();
        
        $this->auth = m::mock(Auth::class);
    }
    
    public function tearDown() {
        m::close();
    }
    
    public function testContract() {
        $areaManager = new AreaManager($this->auth);
        $this->assertInstanceOf(AreaManagerContract::class, $areaManager);
    }
    
    public function testAreas() {
        $areaManager = new AreaManager($this->auth);
        $areas = $areaManager->getAreas();
        
        $this->assertInternalType('array', $areas);
        $this->assertCount(2, $areas);
        $this->assertInstanceOf(AreaContract::class, $areas[0]);
    }
    
    public function testGetAreaById() {
        $clientArea     = new Area('client', 'Client Area');
        $adminArea      = new Area('admin', 'Admin Area');
        $areaManager    = new AreaManager($this->auth);
        
        $this->assertEquals($clientArea, $areaManager->getById('client'));
        $this->assertEquals($adminArea, $areaManager->getById('admin'));
        $this->assertNull($areaManager->getById('dump'));
    }
    
    public function testAdminArea() {
        $this->markTestIncomplete('This test has not been implemented yet because of wrong isAny method implementation.');
        
        $this->auth->shouldReceive('isAny')->withArgs(['member'])->once()->andReturn(false);
        
        $adminArea      = new Area('admin', 'Admin Area');
        $areaManager    = new AreaManager($this->auth);
        
        $this->assertTrue($areaManager->isAdminArea());
        $this->assertFalse($areaManager->isClientArea());
        $this->assertEquals($adminArea, $areaManager->getCurrentArea());
    }
    
    public function testClientArea() {
        $this->markTestIncomplete('This test has not been implemented yet because of wrong isAny method implementation.');
        
        $this->auth->shouldReceive('isAny')->withArgs(['member'])->once()->andReturn(true);
        
        $clientArea     = new Area('client', 'Client Area');
        $areaManager    = new AreaManager($this->auth);
        
        $this->assertTrue($areaManager->isClientArea());
        $this->assertFalse($areaManager->isAdminArea());
        $this->assertEquals($clientArea, $areaManager->getCurrentArea());
    }
    
}

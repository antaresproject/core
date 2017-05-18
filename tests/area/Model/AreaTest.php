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


namespace Antares\Area\Tests\Model;

use Antares\Testing\TestCase;
use Antares\Area\Contracts\AreaContract;
use Antares\Area\Model\Area;

class AreaTest extends TestCase {
    
    public function testContract() {
        $area = new Area('client', 'Client Area');
        
        $this->assertInstanceOf(AreaContract::class, $area);
    }
    
    public function testMethods() {
        $area = new Area('client', 'Client Area');
        
        $this->assertSame('client', $area->getId());
        $this->assertSame('Client Area', $area->getLabel());
        $this->assertSame($area->getLabel(), (string) $area);
    }
    
    public function testNotEqualComparison() {
        $clientArea = new Area('client', 'Client Area');
        $adminArea  = new Area('admin', 'Admin Area');
        
        $this->assertFalse($adminArea->isEquals($clientArea));
    }
    
    public function testEqualComparison() {
        $area1 = new Area('client', 'Client Area');
        $area2 = new Area('client', 'Client Area');
        
        $this->assertTrue($area1->isEquals($area2));
    }
}
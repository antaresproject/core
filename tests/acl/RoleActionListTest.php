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

use Antares\Acl\RoleActionList;
use Antares\Acl\Action;

class RoleActionListTest extends \PHPUnit_Framework_TestCase {
    
    protected $listA;
    protected $listB;
    
    public function setUp() {
        $this->listA = [
            new Action('aa', 'A a'),
            new Action('bb', 'B b'),
            new Action('cc', 'C c'),
        ];
        
        $this->listB = [
            new Action('dd', 'D d'),
            new Action('ee', 'E e'),
            new Action('ff', 'F f'),
        ];
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowException() {
        $list = new RoleActionList();
        $list->add(['role'], []);
        
        $this->setExpectedException('InvalidArgumentException');
    }
    
    public function testAddRole() {
        $list = new RoleActionList;
        $list->add('roleA', $this->listA);
        $list->add('roleB', $this->listB);
        $list->add('roleC', []);
        
        $this->assertEquals($this->listA, $list->getActionsByRole('roleA'));
        $this->assertEquals($this->listB, $list->getActionsByRole('roleB'));
        $this->assertEquals([], $list->getActionsByRole('roleC'));
    }
    
    public function testAddRoleToExistingOnce() {
        $list = new RoleActionList;
        $list->add('roleA', $this->listA);
        $list->add('roleA', $this->listB);
        
        $actions = array_merge($this->listA, $this->listB);
        
        $this->assertEquals($actions, $list->getActionsByRole('roleA'));
    }
    
    public function testAddRoleWithAllActions() {
        $list = new RoleActionList;
        $list->add('roleA', $this->listA);
        $list->add('roleB', $this->listA);
        
        $this->assertEquals($this->listA, $list->getActions());
    }
    
    public function testGetRoles() {
        $list = new RoleActionList;
        $list->add('roleA', $this->listA);
        $list->add('roleB', $this->listA);
        
        $roles = ['roleA', 'roleB'];
        
        $this->assertEquals($roles, $list->getRoles());
    }
    
    public function testGetList() {
        $list = new RoleActionList;
        $list->add('roleA', $this->listA);
        $list->add('roleB', $this->listB);
        
        $testList = [
            'roleA' => $this->listA,
            'roleB' => $this->listB,
        ];
        
        $this->assertEquals($testList, $list->getList());
    }
    
}

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


namespace Antares\Acl;

use InvalidArgumentException;

class RoleActionList {
    
    /**
     *
     * @var array
     */
    protected $list;
    
    /**
     * 
     * @param string $role
     * @param Action[] $actions
     * @throws InvalidArgumentException
     */
    public function add($role, array $actions) {
        if( ! is_string($role) ) {
            throw new InvalidArgumentException('The role argument must be a string.');
        }
        
        $roleActions    = $this->getActionsByRole($role);
        $validActions   = [];

        foreach($actions as $action) {
            if($action instanceof Action) {
                $validActions[] = $action;
            }
        }

        
        $this->list[$role] = array_merge($roleActions, $validActions);
    }
    
    /**
     * 
     * @param string $role
     * @return Action[]
     */
    public function getActionsByRole($role) {
        return array_get($this->list, $role, []);
    }
    
    /**
     * 
     * @return Action[]
     */
    public function getActions() {
        $actions = [];
        
        foreach($this->list as $roleActions) {
            $actions = array_merge($actions, $roleActions);
        }
        
        return array_map('unserialize', array_unique(array_map('serialize', $actions)));
    }
    
    /**
     * 
     * @return array
     */
    public function getRoles() {
        return array_keys($this->list);
    }
    
    /**
     * 
     * @return array
     */
    public function getList() {
        return $this->list;
    }
    
}

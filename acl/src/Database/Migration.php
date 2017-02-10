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


namespace Antares\Acl\Database;

use Illuminate\Container\Container;
use Antares\Acl\RoleActionList;

class Migration {
    
    /**
     *
     * @var Container 
     */
    protected $container;
    
    /**
     *
     * @var string
     */
    protected $componentName;

    /**
     * Migration constructor.
     *
     * @param Container $container
     * @param $componentName
     */
    public function __construct(Container $container, $componentName) {
        $this->container        = $container;
        $this->componentName    = $componentName;
    }

    /**
     * Returns the Authorization Factory instance.
     *
     * @return \Antares\Authorization\Factory
     */
    protected function getAcl() {
        return $this->container->make('antares.acl');
    }

    /**
     * Returns the Memory Provider instance.
     *
     * @return \Antares\Memory\Provider
     */
    protected function getProviderMemory() {
        return $this->container->make('antares.memory')->make('component');
    }
    
    /**
     * Set up permissions to the component ACL memory.
     *
     * @param RoleActionList $roleActionList
     */
    public function up(RoleActionList $roleActionList) {
        $memory     = $this->getProviderMemory();
        $acl        = $this->getAcl()->make('antares/' . $this->componentName);
        $roles      = $roleActionList->getRoles();
        $actions    = $roleActionList->getActions();

        $acl->attach($memory);
        $acl->roles()->attach($roles);
        $acl->actions()->attach(self::getFlatActions($actions));

        foreach($roles as $role) {
            $roleActions = self::getFlatActions($roleActionList->getActionsByRole($role));
            $acl->allow($role, $roleActions);
        }

        $memory->finish();
    }

    /**
     * Tear down permissions from the component ACL memory.
     */
    public function down() {
        $this->getProviderMemory()->forget('acl_antares/' . $this->componentName);
    }

    /**
     * Returns a flatten array of actions which only contains friendly action names.
     *
     * @param array $actions
     * @return array
     */
    protected static function getFlatActions(array $actions) {
        $_actions = [];

        foreach($actions as $action) {
            $_actions[] = $action->getAction();
        }

        return array_unique($_actions);
    }
    
}

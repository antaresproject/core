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

namespace Antares\Acl;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class RoleActionList
{

    /**
     * Roles actions list.
     *
     * @var Action[][]
     */
    protected $list = [];

    /**
     * Action descriptions list
     *
     * @var array
     */
    protected $descriptions = [];

    /**
     * Action categories list
     *
     * @var array
     */
    protected $categories = [];

    /**
     * @param string $role
     * @param Action[] $actions
     * @throws InvalidArgumentException
     */
    public function add($role, array $actions)
    {
        if (!is_string($role)) {
            throw new InvalidArgumentException('The role argument must be a string.');
        }

        $roleActions  = $this->getActionsByRole($role);
        $validActions = [];

        foreach ($actions as $action) {
            if ($action instanceof Action) {
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
    public function getActionsByRole($role)
    {
        return Arr::get($this->list, $role, []);
    }

    /**
     * 
     * @return Action[]
     */
    public function getActions()
    {
        $actions = call_user_func_array('array_merge', $this->list);

        return array_map('unserialize', array_unique(array_map('serialize', $actions)));
    }

    /**
     * 
     * @return array
     */
    public function getRoles()
    {
        return array_keys($this->list);
    }

    /**
     * 
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Adds descriptions to action list
     * 
     * @param array $descriptions
     * @return $this
     */
    public function addDescriptions(array $descriptions = [])
    {
        $this->descriptions = $descriptions;
        return $this;
    }

    /**
     * Adds categories to action list
     * 
     * @param array $categories
     * @return $this
     */
    public function addCategories(array $categories = [])
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Categories getter
     * 
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Descriptions getter
     * 
     * @return array
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

}

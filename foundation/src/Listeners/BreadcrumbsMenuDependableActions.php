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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Listeners;

use Antares\Support\Fluent;
use Closure;

class BreadcrumbsMenuDependableActions extends AbstractDependableActions
{

    /**
     * Instance of entity in menu
     *
     * @var mixed 
     */
    protected $entity;

    /**
     * Menu instance
     *
     * @var \Antares\Foundation\Support\MenuHandler 
     */
    protected $menu;

    /**
     * Handle when event firing
     * 
     * @param \Antares\Foundation\Support\MenuHandler $menu
     * @return void
     */
    public function handle($menu)
    {
        $this->menu = $menu;
        $attributes = $menu->getAttributes();
        if (is_null($entity     = array_get($attributes, 'entity')) or empty($dependable = $this->actions)) {
            return;
        }
        $this->entity = $entity;
        $items        = [];
        foreach ($dependable as $classname => $actions) {
            if (get_class($entity) !== $classname) {
                continue;
            }
            array_push($items, $actions);
            $this->add($attributes, $actions);
        }
    }

    /**
     * Adds menu item to breadcrumb menu
     * 
     * @param MenuHandler $menu
     * @param array $attributes
     * @param array $actions
     */
    protected function add($attributes, $actions)
    {
        foreach ($actions as $name => $callback) {
            if (!$callback instanceof Closure) {
                continue;
            }
            $called = call_user_func($callback, $this->entity);
            if (empty($called) or ! is_array($called)) {
                continue;
            }
            $fluent = $this->createMenuItem($name, $called);
            array_set($attributes, 'childs.dependable-action', $fluent);
            $this->menu->offsetSet('attributes', $attributes);
        }
    }

    /**
     * Creates menu item as Fluent object
     * 
     * @param String $name
     * @param array $attributes
     * @return Fluent
     */
    protected function createMenuItem($name, $attributes)
    {
        $icon = array_get($attributes, 'attributes.data-icon', '');
        return new Fluent([
            "icon"   => starts_with($icon, 'zmdi') ? $icon : 'zmdi-' . $icon,
            "link"   => array_get($attributes, 'url'),
            "title"  => array_get($attributes, 'title'),
            "id"     => $name,
            "childs" => []
        ]);
    }

}

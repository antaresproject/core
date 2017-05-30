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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Http\Handlers;

use Antares\Foundation\Support\MenuHandler;
use Antares\Contracts\Authorization\Authorization;
use Exception;

class ComponentsSelector extends MenuHandler
{

    /**
     * Menu configuration.
     *
     * @var array
     */
    protected $menu = [
        'id'       => 'ui-components-selector',
        'position' => '>:settings',
        'title'    => 'Select ui component',
        'link'     => '#',
        'icon'     => 'icon-support',
        'boot'     => [
            'group' => 'menu.top.right',
            'on'    => 'antares/foundation::*',
            'view'  => 'antares/ui-components::admin.partials._menu'
        ]
    ];

    /**
     * Get the title.
     * @param  string  $value
     * @return string
     */
    public function getTitleAttribute($value)
    {
        return $this->container->make('translator')->trans($value);
    }

    /**
     * Check authorization to display the menu.
     * 
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     * @return bool
     */
    public function authorize(Authorization $acl)
    {
        return !auth()->guest();
    }

    /**
     * get page id by route
     * 
     * @return boolean | int
     */
    protected function getPageId()
    {
        $route = call_user_func($this->container->make('request')->getRouteResolver());
        if (is_null($route)) {
            return false;
        }
        $action = $route->getAction();

        $controller = isset($action['controller']) ? $action['controller'] : null;
        if (is_null($controller)) {
            return false;
        }

        $resource = $this->container->make('config')->get('antares/ui-components::pages.resource');
        if ($controller == $resource) {
            return $route->parameter('id');
        }
        return false;
    }

    /**
     * Create a handler.
     * 
     * @return void
     */
    public function handle()
    {
        if (!$this->passesAuthorization()) {
            return;
        }
        $uri        = uri();
        $handler    = $this->createMenu();
        $handler->uri($uri);
        $components = app('ui-components')->findAllByResource($uri);
        foreach ($components as $component) {
            if (array_get($component, 'data.dispatchable') === false) {
                continue;
            }
            if (!$component['name']) {
                continue;
            }

            $this->createItem($component);
        }
    }

    /**
     * create menu item by ui component attributes
     * 
     * @param array $component
     * @return null
     */
    protected function createItem(array $component)
    {
        $classname = array_get($component, 'data.classname');


        if (!$this->dispatchable($classname)) {
            return;
        }
        $id   = $component['id'];
        $attr = array_get($component, 'data', []);
        $this->handler->add($id, '^:ui-components-selector')
                ->title($attr['name'])
                ->link(handles("antares/ui-components::ui-components/view/{$id}", ['csrf' => true]))
                ->icon('fa fa-plus-circle')
                ->attributes(array_only($attr, ['x', 'y', 'width', 'height', 'disabled']) + ['rel' => $id, 'classname' => $classname]);
    }

    /**
     * Does the ui component is dispatchable
     * 
     * @param String $className
     * @return boolean
     */
    protected function dispatchable($className)
    {
        try {
            return class_exists($className);
        } catch (Exception $ex) {
            return false;
        }
    }

}

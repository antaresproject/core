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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Listeners;

use Closure;

class DatatableMassActionsDependableActions extends DatatableDependableActions
{

    /**
     * Configuration container
     *
     * @var array 
     */
    protected $actions;

    /**
     * Construct
     * 
     * @param Application $app
     */
    public function __construct()
    {
        $this->actions = config('dependable_actions.mass_actions');
    }

    /**
     * Add dependable action in datatable context menu
     * 
     * @param array $actions
     * @param \Illuminate\Database\Eloquent\Model $row
     * @param Closure $element
     * @return boolean
     */
    protected function resolveDatatableAction(&$actions, $row, $element)
    {
        if (!$element instanceof Closure) {
            return false;
        }
        $action = call_user_func($element, $row);
        $url    = handles(array_get($action, 'url', '#'));
        $title  = array_get($action, 'title');
        $icon   = array_get($action, 'attributes.data-icon');
        $html   = app('html');
        if (strlen($icon)) {
            $title = $html->raw('<i class="zmdi ' . (starts_with($icon, 'zmdi') ? $icon : 'zmdi-' . $icon) . '"></i><span>' . $title . '</span>');
        }
        $attributes = array_get($action, 'attributes', []);
        $this->addClass('mass-action', $attributes);
        $link       = $html->link($url, $title, $attributes);
        ($actions instanceof Collection) ? $actions->push($link) : array_push($actions, $link);
        return $actions;
    }

    /**
     * Add css class to link 
     * 
     * @param String $cssClass
     * @param String $attributes
     * @return mixed
     */
    protected function addClass($cssClass, &$attributes)
    {
        $class = array_get($attributes, 'class', '');
        if (!str_contains($class, $cssClass)) {
            $class .= ' ' . $cssClass;
        }
        return array_set($attributes, 'class', $class);
    }

}

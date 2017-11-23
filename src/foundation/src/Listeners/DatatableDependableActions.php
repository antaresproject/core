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

use Antares\Events\Datatables\AfterMassActionsAction;
use Antares\Events\Datatables\AfterTableAction;
use Antares\Events\Datatables\BeforeMassActionsAction;
use Antares\Events\Datatables\BeforeTableAction;
use Illuminate\Support\Collection;
use Closure;

class DatatableDependableActions extends AbstractDependableActions
{

    /**
     * Handle when event firing
     *
     * @param $eventName
     * @param array $params
     * @return array
     */
    public function handle($event)
    {
        switch (get_class($event)) {
            case AfterMassActionsAction::class:
                $actions = $event->massActions;
                $row = $event->model;
                break;

            case BeforeMassActionsAction::class:
                $actions = $event->massActions;
                $row = $event->model;
                break;

            case AfterTableAction::class:
                $actions = $event->tableActions;
                $row = $event->row;
                break;

            case BeforeTableAction::class:
                $actions = $event->tableActions;
                $row = $event->row;
                break;
        }

        $elements = $this->getActions($row);
        $return   = [];

        foreach ($elements as $element) {
            $resolved = $this->resolveDatatableAction($actions, $row, $element);
            if (is_array($resolved)) {
                $return[] = $resolved;
            }
        }


        return count($return) ? array_merge(...$return) : [];
    }

    /**
     * Gets datatable actions
     * 
     * @param \Illuminate\Database\Eloquent\Model $row
     * @return array
     */
    protected function getActions($row)
    {
        $classname = get_class($row);
        return (array_key_exists($classname, $this->actions)) ? array_get($this->actions, $classname, []) : [];
    }

    /**
     * Add dependable action in datatable context menu
     * 
     * @param array $actions
     * @param \Illuminate\Database\Eloquent\Model $row
     * @param Closure $element
     * @return boolean
     */
    protected function resolveDatatableAction($actions, $row, $element)
    {
        if (!$element instanceof Closure) {
            return false;
        }
        $action = call_user_func($element, $row);
        if (empty($action)) {
            return false;
        }
        $url   = handles(array_get($action, 'url', '#'));
        $title = array_get($action, 'title');
        $link  = app('html')->link($url, $title, array_get($action, 'attributes', []));
        ($actions instanceof Collection) ? $actions->push($link) : array_push($actions, $link);
        return $actions;
    }

}

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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Customfields\Http\Handlers;

use Antares\Foundation\Http\Composers\LeftPane as SupportLeftPane;
use Antares\Customfields\Model\FieldView;
use Illuminate\Container\Container;

class LeftPane extends SupportLeftPane
{

    /**
     * @var ModulesPane 
     */
    private static $oInstance = false;

    /**
     * menu handler instance
     *
     * @var \Menu\MenuHandler
     */
    private static $menuInstance = false;

    /**
     * @return ModulesPane
     */
    public static function getInstance()
    {
        if (self::$oInstance == false) {
            self::$oInstance = new self();
        }
        return self::$oInstance;
    }

    /**
     * Handle pane for dashboard page.
     *
     * @return void
     */
    public function compose($name = null, $options = array())
    {
        if (!self::$menuInstance) {

            $id       = from_route('customfields');
            $category = from_route('category');
            $fields   = FieldView::query()->get(['id', 'category_name'])->groupBy('category_name');
            $menu     = app('antares.widget')->make('menu.customfields.pane');
            foreach ($fields as $name => $items) {
                $active = false;
                if (!is_null($id)) {
                    foreach ($items as $item) {
                        if ($item->id != $id) {
                            continue;
                        }
                        $active = true;
                        break;
                    }
                } else {
                    $active = $category == $name;
                }

                $menu->add($name)
                        ->link(handles('antares::customfields/' . $name . '/index'))
                        ->title(ucfirst($name) . ' (' . $items->count() . ')')
                        ->active($active);
            }
            self::$menuInstance = $menu;
            $app                = Container::getInstance();
            $pane               = $app->make('antares.widget')->make('pane.left');
            $pane->add('logger')->content(view('antares/customfields::admin.partials._left_pane'));
        }
    }

}

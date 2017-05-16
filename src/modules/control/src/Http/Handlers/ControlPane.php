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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Control\Http\Handlers;

use Antares\Foundation\Http\Composers\LeftPane;

class ControlPane extends LeftPane
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

            $menu = app('antares.widget')->make('menu.control.pane');
            $auth = app('auth');

            $acl                  = app('antares.acl')->make('antares/control');
            $canAdministratorList = $auth->is('super-administrator') && $acl->can('admin-list');
            $canRoleList          = $acl->can('roles-list');
            if (!$canAdministratorList and ! $canRoleList) {
                return;
            }

            $menu->add('general-settings')
                    ->link(handles('antares::settings/index'))
                    ->title(trans('System'))
                    ->icon('zmdi-settings');


            self::$menuInstance = $menu;
            $pane               = app()->make('antares.widget')->make('pane.left');
            $pane->add('control')->content(view('antares/control::partial._control_pane'));
        }
    }

}

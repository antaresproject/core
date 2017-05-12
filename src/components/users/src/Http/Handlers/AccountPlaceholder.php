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


namespace Antares\Users\Http\Handlers;

use Antares\Foundation\Http\Composers\LeftPane;

class AccountPlaceholder extends LeftPane
{

    /**
     * Handle pane for dashboard page.
     *
     * @return void
     */
    public function compose($name = null, $options = array())
    {
        $menu = app('antares.widget')->make('menu.control.pane');

        $menu->add('account')
                ->link(handles('antares/foundation::account'))
                ->title(trans('My Account'))
                ->icon('zmdi-settings');

        $menu->add('devices')
                ->link(handles('antares::logger/devices/index'))
                ->title(trans('Devices'))
                ->icon('zmdi-devices');

        $pane = app()->make('antares.widget')->make('pane.left');
        $pane->add('control')->content(view('antares/control::partial._control_pane'));
    }

}

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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Updater\Http\Handlers;

use Antares\Foundation\Http\Composers\LeftPane;

class TranslationsPane extends LeftPane
{

    /**
     * panel left for translations
     *
     * @return void
     */
    public function compose($name = null, $options = array())
    {
        $menu  = app('antares.widget')->make('menu.translations.pane');
        $areas = config('areas.areas');
        $area  = request()->segment(4);
        foreach ($areas as $name => $title) {
            $menu->add($name)
                    ->link(handles('antares::translations/index/' . $name))
                    ->title(trans($title))
                    ->icon('zmdi-accounts')
                    ->active($area == $name);
        }
        app('antares.widget')->make('pane.left')->add('translations')->content(view('antares/translations::admin.partials._left_pane'));
    }

}

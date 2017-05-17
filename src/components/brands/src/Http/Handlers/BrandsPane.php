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

namespace Antares\Brands\Http\Handlers;

use Antares\Foundation\Http\Composers\LeftPane;
use Antares\Brands\Model\BrandTemplates;

class BrandsPane extends LeftPane
{

    /**
     * Handle pane for dashboard page.
     *
     * @return void
     */
    public function compose($name = NULL, $options = array())
    {
        $menu = $this->widget->make('menu.brands.pane');
        $id   = request()->segment(2) == 'branding' ? brand_id() : from_route('brands');

        $menu->add('brand-settings')
                ->link(handles("antares::branding"))
                ->title(trans('antares/brands::messages.brand_settings'));

        $menu->add('brand-email')
                ->link(handles("antares::branding/email"))
                ->title(trans('antares/brands::messages.brand_settings_email'));

        $areas     = config('areas.areas');
        $templates = BrandTemplates::where('brand_id', $id)->whereIn('area', array_keys($areas))->get();

        foreach ($templates as $template) {
            $menu->add($template->area)
                    ->title(trans(array_get($areas, $template->area) . ' Area'))
                    ->link(handles("antares::branding/area/{$template->id}"));
        }

        $this->widget->make('pane.left')->add('brands')->content(view('antares/foundation::components.placeholder_left')->with('menu', $menu));
    }

}

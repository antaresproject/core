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

namespace Antares\Brands\Http\Breadcrumb;

use Antares\Brands\Model\Brands as Model;
use Antares\Breadcrumb\Navigation;

class Breadcrumb extends Navigation
{

    /**
     * on brands list
     */
    public function onBrandsList()
    {
        $this->breadcrumbs->register('brands', function($breadcrumbs) {
            $url = extension_active('multibrand') ? 'antares::multibrand/index' : 'antares::brands/' . from_route('brands') . '/edit';
            $breadcrumbs->push('Branding', handles($url));
        });

        $this->shareOnView('brands');
    }

    /**
     * on brand create or edit
     * 
     * @param Model $model
     */
    public function onBrandEdit(Model $model)
    {
        $this->onBrandsList();
        $name = 'rand-' . $model->name;
        $this->breadcrumbs->register($name, function($breadcrumbs) use($model, $name) {
            $multibrandActive = extension_active('multibrand');
            $name             = trans('antares/brands::messages.brand_settings');
            if ($multibrandActive) {
                $breadcrumbs->parent('brands');
                $name = trans('antares/brands::messages.brand_edit', ['name' => $model->name]);
            }
            $breadcrumbs->push($name);
        });
        $this->shareOnView($name);
    }

    /**
     * On brand email edit
     * 
     * @param Model $model
     */
    public function onBrandEmailEdit(Model $model)
    {
        $this->onBrandsList();
        $name = 'rand-' . $model->name;
        $this->breadcrumbs->register($name, function($breadcrumbs) use($model, $name) {
            $breadcrumbs->parent('brands');
            $multibrandActive = extension_active('multibrand');
            $name             = trans('antares/brands::messages.brand_settings_email');
            if ($multibrandActive) {
                $name = trans('antares/brands::messages.brand_settings_multibrand_email', ['name' => $model->name]);
            }
            $breadcrumbs->push($name);
        });
        $this->shareOnView($name);
    }

    /**
     * On brand area edit
     * 
     * @param Model $model
     */
    public function onArea(Model $model)
    {
        $this->onBrandsList();
        $this->breadcrumbs->register('brand-area', function($breadcrumbs) use($model) {
            $name = array_get(config('areas.areas'), $model->templates->first()->area);
            $breadcrumbs->parent('brands');
            $breadcrumbs->push(trans('antares/brands::messages.brand_area_settings', ['name' => $name]));
        });
        $this->shareOnView('brand-area');
    }

}

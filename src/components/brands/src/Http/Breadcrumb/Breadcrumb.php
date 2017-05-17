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

namespace Antares\Brands\Http\Breadcrumb;

use Antares\Brands\Model\Brands as Model;
use Antares\Breadcrumb\Navigation;

class Breadcrumb extends Navigation
{

    /**
     * On init brands
     */
    public function onInit()
    {
        $this->breadcrumbs->register('branding', function($breadcrumbs) {
            $breadcrumbs->push('Branding', handles('antares::branding'));
        });
        $this->shareOnView('branding');
    }

    /**
     * on brands list
     */
    public function onBrandsList()
    {
        $this->onInit();
        $this->breadcrumbs->register('brands', function($breadcrumbs) {
            $breadcrumbs->parent('branding');
            $url = extension_active('multibrand') ? 'antares::multibrand/index' : 'antares::branding';
            $breadcrumbs->push('Brand settings', handles($url));
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
            $breadcrumbs->parent('branding');
            $multibrandActive = extension_active('multibrand');
            $name             = trans('antares/brands::messages.brand_settings');
            if ($multibrandActive) {
                $breadcrumbs->parent('brands');
                $name = trans('antares/brands::messages.brand_edit', ['name' => $model->name]);
            }
            set_meta('title', $name);
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
            $multibrandActive = extension_active('multibrand');
            $breadcrumbs->parent($multibrandActive ? 'brands' : 'branding');

            $name = trans('antares/brands::messages.brand_settings_email');
            if ($multibrandActive) {
                $name = trans('antares/brands::messages.brand_settings_multibrand_email', ['name' => $model->name]);
            }
            set_meta('title', $name);
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
            $name  = array_get(config('areas.areas'), $model->templates->first()->area);
            $breadcrumbs->parent(extension_active('multibrand') ? 'brands' : 'branding');
            $title = trans('antares/brands::messages.brand_area_settings', ['name' => $name]);
            set_meta('title', $title);
            $breadcrumbs->push($title);
        });
        $this->shareOnView('brand-area');
    }

}

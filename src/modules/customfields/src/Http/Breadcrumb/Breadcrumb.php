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



namespace Antares\Customfields\Http\Breadcrumb;

use DaveJamesMiller\Breadcrumbs\Facade as Breadcrumbs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Breadcrumb
{

    /**
     * on list customfields without category
     */
    protected function onList()
    {
        Breadcrumbs::register('customfields', function($breadcrumbs) {
            $breadcrumbs->push('Customfields', handles('antares::customfields'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('customfields'));
    }

    /**
     * when shows edit or create custom field form
     * 
     * @param Model $model
     */
    public function onCustomFieldCreateOrEdit(Model $model)
    {
        $this->onList();
        Breadcrumbs::register('customfields-create-update', function($breadcrumbs) use($model) {
            $breadcrumbs->parent('customfields');
            $name = $model->exists ? 'Update custom field ' . $model->name : 'Create custom field';
            $breadcrumbs->push($name, '#');
        });

        view()->share('breadcrumbs', Breadcrumbs::render('customfields-create-update'));
    }

}

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

namespace Antares\Customfields\Http\Presenters;

use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Customfields\Http\Datatables\Customfields;
use Antares\Customfields\Http\Forms\FieldFormFactory;
use Antares\Customfields\Http\Breadcrumb\Breadcrumb;
use Antares\Foundation\Http\Presenters\Presenter;
use Antares\Support\Facades\Foundation;
use Illuminate\Routing\Route;

class FieldPresenter extends Presenter
{

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * customfields datatable instance
     *
     * @var Customfields 
     */
    protected $datatable;

    /**
     * constructing a new customfield presenter
     * 
     * @param FormFactory $form
     */
    public function __construct(FieldFormFactory $form, Breadcrumb $breadcrumb, Customfields $datatable)
    {
        $this->form       = $form;
        $this->breadcrumb = $breadcrumb;
        $this->datatable  = $datatable;
    }

    /**
     * Table View Generator for Antares\Model\Customfields.
     * 
     * @return \Illuminate\View\View
     */
    public function table()
    {
        return $this->datatable->render('antares/customfields::admin.list');
    }

    /**
     * @todo refaktoryzacja
     * @param type $model
     * @param Route $route
     * @return type
     */
    protected function prepareData($model, Route $route)
    {
        /**
         * prepare data
         */
        $categoryModel = Foundation::make('antares.customfields.model.category');
        $categoryId    = $route->parameter('category');
        if (!is_null($categoryId)) {
            $default = $categoryModel->query()->findOrFail($categoryId);
        } elseif ($model->exists) {
            $default = $model->groups->category;
        } else {
            $default = $categoryModel::getDefault();
        }

        $type        = Foundation::make('antares.customfields.model.type');
        $typeOptions = function() use($type) {
            $types   = $type->get();
            $options = [];
            foreach ($types as $model) {
                $namedOption         = $model->type != '' ? $model->name . ':' . $model->type : $model->name;
                $options[$model->id] = $namedOption;
            }
            return $options;
        };
        $typeId = $route->parameter('type');
        if (!is_null($typeId)) {
            $defaultField = $type->query()->findOrFail($typeId);
        } elseif ($model->exists) {
            $defaultField = $model->types;
            $typeId       = $model->type_id;
        } else {
            $defaultField = $type::getDefault();
        }

        $groupId         = $route->parameter('group');
        $categoryOptions = $categoryModel::pluck('name', 'id');

        return [
            'groupId'             => $groupId,
            'typeId'              => $typeId,
            'categoryId'          => $default->id,
            'typeOptions'         => $typeOptions,
            'categoryOptions'     => $categoryOptions,
            'groupOptions'        => $default->group->pluck('name', 'id'),
            'availableValidators' => $defaultField->validators,
            'activeValidators'    => $model->getFlattenValidators(),
            'multi'               => $defaultField->multi,
            'options'             => $model->options
        ];
    }

    /**
     * publishing form
     * @param type $model
     * @return type
     */
    public function form($model, $actionName, Route $route)
    {
        $this->breadcrumb->onCustomFieldCreateOrEdit($model);
        publish('customfields', ['js/on.init.form.js']);
        $prepared = $this->prepareData($model, $route);
        return $this->form->build($this, $model, $prepared);
    }

}

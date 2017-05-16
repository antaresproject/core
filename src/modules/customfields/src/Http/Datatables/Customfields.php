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

namespace Antares\Customfields\Http\Datatables;

use Antares\Customfields\Filter\CustomfieldsFilter;
use Antares\Datatables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;
use Antares\Support\Facades\Foundation;

class Customfields extends DataTable
{

    /**
     * items per page
     *
     * @var mixed 
     */
    public $perPage = 25;

    /**
     * customfields filters
     *
     * @var array 
     */
    protected $filters = [
        CustomfieldsFilter::class
    ];

    /**
     * Quick search settings
     *
     * @var String
     */
    protected $search = [
        'view'     => 'antares/customfields::admin.partials._search',
        'category' => 'Custom fields'
    ];

    /**
     * @return Builder
     */
    public function query()
    {
        $where    = ['brand_id' => brand_id()];
        if (!is_null($category = from_route('category'))) {
            array_set($where, 'category_name', $category);
        }
        $builder = Foundation::make('antares.customfields.model.view')->where($where);

        $customfields = $builder->get();

        $configurable = app('customfields')->getConfigurable($category);
        foreach ($customfields as $index => $customfield) {
            if (!$customfield->imported) {
                continue;
            }
            $in = (is_null($category) && isset($configurable[$customfield->category_name])) ? $configurable[$customfield->category_name] : $configurable;
            if (!in_array($customfield->name, $in)) {
                $customfields->forget($index);
            }
        }
        return $customfields;
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        return $this->prepare()
                        ->addColumn('action', $this->getActionsColumn())
                        ->editColumn('type', function($row) {
                            return is_null($row->type) ? '---' : $row->type;
                        })
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Customfields List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('antares/customfields::datagrid.header.id')])
                        ->addColumn(['data' => 'name', 'name' => 'name', 'title' => trans('antares/customfields::datagrid.header.name'), 'className' => 'bolded'])
                        ->addColumn(['data' => 'group_name', 'name' => 'group_name', 'title' => trans('antares/customfields::datagrid.header.group_name')])
                        ->addColumn(['data' => 'type_name', 'name' => 'type_name', 'title' => trans('antares/customfields::datagrid.header.type_name')])
                        ->addColumn(['data' => 'type', 'name' => 'type', 'title' => trans('antares/customfields::datagrid.header.type')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->ajax(handles('antares::customfields/index'));
    }

    /**
     * Get actions column for table builder.
     * 
     * @return callable
     */
    protected function getActionsColumn()
    {
        return function ($row) {
            $btns    = [];
            $html    = app('html');
            $btns[]  = $html->create('li', $html->link(handles("antares::customfields/{$row->id}/edit"), trans('antares/brands::label.brand.edit'), ['data-icon' => 'edit']));
            $btns[]  = $html->create('li', $html->link(handles("antares::customfields/{$row->id}/delete", ['csrf' => true]), trans('antares/brands::label.brand.delete'), ['class' => "triggerable confirm", 'data-icon' => 'delete', 'data-title' => trans("Are you sure?"), 'data-description' => trans('Deleteing customfield') . ' ' . $row->name]));
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . app('html')->raw($section)->get();
        };
    }

    /**
     * Gets patterned url for search engines
     * 
     * @return String
     */
    public static function getPatternUrl()
    {
        return handles('antares::customfields/{id}/edit');
    }

}

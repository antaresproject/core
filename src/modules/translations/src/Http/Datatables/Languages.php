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



namespace Antares\Translations\Http\Datatables;

use Antares\Translations\Models\Languages as LanguagesModel;
use Antares\Datatables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;

class Languages extends DataTable
{

    /**
     * items per page
     *
     * @var mixed 
     */
    public $perPage = 10;

    /**
     * @return Builder
     */
    public function query()
    {
        return LanguagesModel::all();
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $canAddLanguage = app('antares.acl')->make('antares/translations')->can('add-language');
        return $this->prepare()
                        ->editColumn('name', function ($model) {
                            $code     = $model->code;
                            $codeIcon = (($code == 'en') ? 'us' : $code);
                            return '<i class="flag-icon flag-icon-' . $codeIcon . '"></i>' . $model->name;
                        })
                        ->editColumn('is_default', function ($model) {
                            return ((int) $model->is_default) ?
                                    '<span class="label-basic label-basic--success">' . trans('Yes') . '</span>' :
                                    '<span class="label-basic label-basic--danger">' . trans('No') . '</span>';
                        })
                        ->addColumn('action', $this->getActionsColumn($canAddLanguage))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this
                        ->setName('Languages List')
                        ->setQuery($this->query())
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Id'])
                        ->addColumn(['data' => 'code', 'name' => 'code', 'title' => 'Country code'])
                        ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Country'])
                        ->addColumn(['data' => 'is_default', 'name' => 'is_default', 'title' => 'Default'])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->setDeferedData();
    }

    /**
     * Actions column for datatable
     * 
     * @param boolean $canAddLanguage
     * @return String
     */
    protected function getActionsColumn($canAddLanguage)
    {
        return function ($row) use($canAddLanguage) {
            $btns = [];
            $html = app('html');
            if ($canAddLanguage && !$row->is_default) {
                $btns[] = $html->create('li', $html->link(handles("antares::translations/languages/delete/" . $row->id), trans('Delete'), ['class' => "triggerable confirm", 'data-icon' => 'delete', 'data-title' => trans("Are you sure?"), 'data-description' => trans('Deleting language') . ' ' . $row->code]));
                $btns[] = $html->create('li', $html->link(handles("antares::translations/languages/default/" . $row->id), trans('Set as default'), ['data-icon' => 'spellcheck']));
            }

            if (empty($btns)) {
                return '';
            }
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}

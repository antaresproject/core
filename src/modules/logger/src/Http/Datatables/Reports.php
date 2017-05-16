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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Http\Datatables;

use Antares\Datatables\Services\DataTable;
use Antares\Logger\Model\Report;

class Reports extends DataTable
{

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        return Report::with('user', 'type')->currentBrand();
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $acl         = app('antares.acl')->make('antares/logger');
        $canView     = $acl->can('report-view');
        $canDownload = $acl->can('report-download');
        $canDelete   = $acl->can('report-delete');
        return $this->prepare()
                        ->editColumn('type_id', function ($item) {
                            return $item->type->name;
                        })->editColumn('user_id', function ($item) {
                            return $item->user->fullname;
                        })->addColumn('action', $this->getActionsColumn($canView, $canDownload, $canDelete))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Reports List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Id'])
                        ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'Name'])
                        ->addColumn(['data' => 'type_id', 'name' => 'type_id', 'title' => 'Type'])
                        ->addColumn(['data' => 'user_id', 'name' => 'user_id', 'title' => 'Author'])
                        ->addColumn(['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Updated At'])
                        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->setDeferedData();
    }

    /**
     * Get actions column for table builder.
     * @return callable
     */
    protected function getActionsColumn($canView, $canDownload, $canDelete)
    {
        return function ($row) use($canView, $canDownload, $canDelete) {
            $btns = [];
            $html = app('html');
            if ($canView) {
                $btns[] = $html->create('li', $html->link(handles("antares::logger/view/" . $row->id), trans('View')));
            }
            if ($canDownload) {
                $btns[] = $html->create('li', $html->link(handles("antares::logger/download/html/" . $row->id, ['csrf' => true]), trans('Download Html'), ['data-icon' => 'download']));
                $btns[] = $html->create('li', $html->link(handles("antares::logger/download/pdf/" . $row->id, ['csrf' => true]), trans('Download PDF'), ['data-icon' => 'download']));
            }
            if ($canDelete) {
                $btns[] = $html->create('li', $html->link(handles("antares::logger/delete/" . $row->id, ['csrf' => true]), trans('Delete'), ['class' => "triggerable confirm", 'data-icon' => 'delete', 'data-title' => trans("Are you sure?"), 'data-description' => trans('Deleteing item') . ' #' . $row->id]));
            }
            if (empty($btns)) {
                return '';
            }
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();

            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}

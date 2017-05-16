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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Automation\Http\Datatables;

use Antares\Datatables\Services\DataTable;
use Antares\Automation\Model\JobResults;

class AutomationDetails extends DataTable
{

    /**
     * items per page
     *
     * @var mixed 
     */
    public $perPage = 25;

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $query = JobResults::where('job_id', from_route('id'));
        if (!app('request')->get('order')) {
            $query->orderBy('created_at', 'desc');
        }
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        return $this->prepare()
                        ->editColumn('has_error', function ($model = null) {
                            return ($model->has_error) ?
                                    '<span class="label-basic label-basic--danger">' . trans('Failure') . '</span>' :
                                    '<span class="label-basic label-basic--success">' . trans('Success') . '</span>';
                        })
                        ->editColumn('return', function ($model) {
                            return '<pre>' . $model->return . '</pre>';
                        })->editColumn('runtime', function ($model) {
                            return $model->runtime . ' s';
                        })
                        ->editColumn('created_at', function ($model) {
                            return format_x_days($model->created_at);
                        })
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Automation Details List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('Id')])
                        ->addColumn(['data' => 'has_error', 'name' => 'has_error', 'title' => trans('Status')])
                        ->addColumn(['data' => 'return', 'name' => 'return', 'title' => trans('Result')])
                        ->addColumn(['data' => 'runtime', 'name' => 'runtime', 'title' => trans('Runtime')])
                        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => trans('Executed at')])
                        ->setDeferedData();
    }

    /**
     * Get actions column for table builder.
     * @return callable
     */
    protected function getActionsColumn($canView, $canRun, $canUpdate)
    {
        return function ($row) use($canView, $canRun, $canUpdate) {
            $btns = [];
            $html = app('html');
            if ($canView) {
                $btns[] = $html->create('li', $html->link(handles("antares::automation/show/" . $row->id), trans('Show details'), ['data-icon' => 'desktop-windows']));
            }
            if ($canRun) {
                $btns[] = $html->create('li', $html->link(handles("antares::automation/run/" . $row->id, ['csrf' => true]), trans('Run'), ['class' => "triggerable confirm", 'data-icon' => 'play-circle-outline', 'data-title' => trans("Are you sure?"), 'data-description' => trans('Running job') . ' #' . $row->id]));
            }
            if ($canUpdate) {
                $btns[] = $html->create('li', $html->link(handles("antares::automation/edit/" . $row->id), trans('Edit'), ['data-icon' => 'edit']));
            }
            if (empty($btns)) {
                return '';
            }
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}

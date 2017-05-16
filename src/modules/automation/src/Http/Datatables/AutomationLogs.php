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

use Antares\Automation\Filter\AutomationDateRangeFilter;
use Antares\Automation\Filter\AutomationLogsFilter;
use Antares\Datatables\Services\DataTable;
use Antares\Automation\Model\JobResults;
use Illuminate\Support\Facades\Event;

class AutomationLogs extends DataTable
{

    /**
     * Definition of available filters
     *
     * @var array 
     */
    protected $filters = [
        AutomationDateRangeFilter::class,
        AutomationLogsFilter::class
    ];

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        Event::listen('datatables.order.status', function($query, $direction) {
            $query->orderBy('has_error', $direction);
        });
        return app(JobResults::class)->select(['tbl_job_results.id', 'tbl_job_results.has_error', 'tbl_job_results.job_id', 'tbl_job_results.return', 'tbl_job_results.runtime', 'tbl_job_results.created_at'])->with('job');
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {

        return $this->prepare()
                        ->filter(function ($query) {
                            $request = app('request');
                            if ($request->ajax() && $request->has('search.value')) {
                                $key = $request->get('search')['value'];
                                $query->leftJoin('tbl_jobs as tj', 'tbl_job_results.job_id', '=', 'tj.id')
                                ->where('tj.name', 'like', "%{$key}%")
                                ->orWhere('tj.value', 'like', "%{$key}%")
                                ->orWhere('tbl_job_results.return', 'like', "%{$key}%");
                            }
                        })
                        ->editColumn('job_id', $this->getJob_idValue())
                        ->editColumn('has_error', $this->getHas_errorValue())
                        ->editColumn('return', function ($model) {
                            $string = $model->return;
                            $class  = (strlen($string) > 70) ? 'class="dots"' : '';
                            return '<span ' . $class . '>' . $model->return . '</span><div class="hidden" rel="' . $model->id . '"><code>' . $model->return . '</code></div>';
                        })->editColumn('runtime', $this->getRuntimeValue())
                        ->editColumn('created_at', function ($model) {
                            return format_x_days($model->created_at);
                        })
                        ->editColumn('action', $this->getActionsColumn())
                        ->make(true);
    }

    /**
     * Gets runtime column value
     * 
     * @return Closure
     */
    public function getRuntimeValue()
    {
        return function($row) {
            return $row->runtime . ' s';
        };
    }

    /**
     * Gets has_error column value
     * 
     * @return Closure
     */
    public function getHas_errorValue()
    {
        return function($row) {
            return ($row->has_error) ?
                    '<span class="label-basic label-basic--danger">' . trans('Failure') . '</span>' :
                    '<span class="label-basic label-basic--success">' . trans('Success') . '</span>';
        };
    }

    /**
     * Gets job_id column value
     * 
     * @return Closure
     */
    public function getJob_idValue()
    {
        return function($row) {
            return isset($row->job) ? $row->job->value['title'] . (($row->job->name) ? ' [ ' . $row->job->name . ' ]' : '') : '---';
        };
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        publish('automation', ['js/automation_logs_table.js']);
        return $this->setName('Automation Logs')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('Id')])
                        ->addColumn(['data' => 'job_id', 'name' => 'job_id', 'title' => trans('Script name'), 'className' => 'bolded'])
                        ->addColumn(['data' => 'has_error', 'name' => 'has_error', 'title' => trans('Status')])
                        ->addColumn(['data' => 'return', 'name' => 'return', 'title' => trans('Result')])
                        ->addColumn(['data' => 'runtime', 'name' => 'runtime', 'title' => trans('Runtime')])
                        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => trans('Executed at')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->setDeferedData();
    }

    /**
     * Get actions column for table builder.
     * 
     * @return callable
     */
    protected function getActionsColumn()
    {
        return function ($row) {
            $html    = app('html');
            $this->addTableAction('show_log', $row, $html->link(handles("antares::automation/show/" . $row->id), trans('antares/automation::messages.show_full_log'), [
                        'data-icon'  => 'desktop-windows',
                        'data-id'    => $row->id,
                        'data-title' => trans('antares/automation::messages.full_log_title', ['script_name' => $row->job->value['title']]),
                        'class'      => 'triggerable show-full-log']));
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $this->tableActions->toArray())))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}

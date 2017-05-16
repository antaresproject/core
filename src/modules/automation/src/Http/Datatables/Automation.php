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

use Antares\Automation\Filter\AutomationStatusFilter;
use Antares\Datatables\Services\DataTable;
use Antares\Automation\Model\JobsCategory;
use Antares\Automation\Model\Jobs;

class Automation extends DataTable
{

    /**
     * available filters
     *
     * @var array 
     */
    protected $filters = [
        AutomationStatusFilter::class
    ];

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
        $builder = app(Jobs::class)->select(['tbl_jobs.*'])->with('jobResults', 'component', 'category');

        listen('datatables.order.title', function($query, $direction) {
            return $query->leftJoin('tbl_components', 'tbl_jobs.component_id', '=', 'tbl_components.id')
                            ->orderBy('tbl_components.full_name', $direction);
        });
        listen('datatables.order.last_run_result', function($query, $direction) {
            return $query->leftJoin('tbl_job_results', 'tbl_jobs.id', '=', 'tbl_job_results.job_id')
                            ->orderBy('tbl_job_results.has_error', $direction);
        });
        listen('datatables.order.last_run', function($query, $direction) {
            return $query->leftJoin('tbl_job_results', 'tbl_jobs.id', '=', 'tbl_job_results.job_id')
                            ->orderBy('tbl_job_results.created_at', $direction);
        });

        return $builder;
    }

    /**
     * Default search builder option
     * 
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setDefaultBuilderOption($builder)
    {
        return $builder->whereHas('category', function($query) {
                    $query->where('name', 'custom');
                });
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $acl       = app('antares.acl')->make('antares/automation');
        $canRun    = $acl->can('automation-run');
        $canView   = $acl->can('automation-details');
        $canUpdate = $acl->can('automation-edit');
        return $this->prepare()
                        ->filter(function($query) {
                            $request = app('request');
                            $keyword = array_get($request->get('search'), 'value');
                            if (is_null($keyword) or ! strlen($keyword)) {
                                return;
                            }
                            $keyword = str_contains($keyword, ': ') ? last(explode(': ', $keyword)) : $keyword;
                            switch ($keyword) {
                                case 'enabled':
                                    $query->where('tbl_jobs.active', 1);
                                    break;
                                case 'disabled':
                                    $query->where('tbl_jobs.active', 0);
                                    break;
                                default:
                                    $columns    = request('columns', []);
                                    $categoryId = null;
                                    array_walk($columns, function($item, $index) use(&$categoryId) {

                                        if (array_get($item, 'data') == 'category_id') {

                                            $categoryId = array_get($item, 'search.value');
                                        }
                                    });
                                    if (!$categoryId) {
                                        $categoryId = $this->findCustomOptionId();
                                    }
                                    $query->where('category_id', $categoryId);


                                    $query
                                    ->leftJoin('tbl_components', 'tbl_jobs.component_id', '=', 'tbl_components.id')
                                    ->leftJoin('tbl_jobs_category', 'tbl_jobs.category_id', '=', 'tbl_jobs_category.id')
                                    ->whereRaw("(tbl_jobs.name like '%$keyword%' or tbl_jobs.value like '%$keyword%' or tbl_components.full_name like '%$keyword%' or tbl_jobs_category.title like '%$keyword%')");
                                    break;
                            }
                        })
                        ->filterColumn('category_id', function($query, $keyword) {
                            if ($keyword !== 'all') {
                                $query->where('category_id', $keyword);
                            }
                        })
                        ->editColumn('title', function ($model) {
                            $name = $model->component->full_name;
                            return ($name ? $name . ' : ' : '') . $model->value['title'];
                        })
                        ->editColumn('description', function ($model) {
                            return $model->value['description'];
                        })
                        ->editColumn('category_id', function ($model) {
                            if (is_null($model->category)) {
                                return '---';
                            }
                            return $model->category->title;
                        })
                        ->editColumn('last_run_result', function ($model) {
                            if ($model->jobResults->isEmpty()) {
                                return '---';
                            }
                            return ($model->jobResults->last()->has_error) ?
                                    '<span class="label-basic label-basic--danger">' . trans('Failure') . '</span>' :
                                    '<span class="label-basic label-basic--success">' . trans('Success') . '</span>';
                        })
                        ->editColumn('last_run', function ($model) {
                            if ($model->jobResults->isEmpty()) {
                                return '---';
                            }
                            return format_x_days($model->jobResults->last()->created_at);
                        })
                        ->editColumn('interval', function ($model) {
                            if (!isset($model->value['launch']) or $model->value['launch'] === false) {
                                return '---';
                            }
                            if (is_array($model->value['launch'])) {
                                $return = '';
                                foreach ($model->value['launch'] as $when => $times) {
                                    $return .= trans('antares/automation::messages.intervals.' . $when, ['value' => implode(',', array_values($times))]);
                                }
                                return $return;
                            }
                            return trans('antares/automation::messages.intervals.' . $model->value['launch']);
                        })->editColumn('active', function ($model) {
                            return ((int) $model->active) ?
                                    '<span class="label-basic label-basic--success">' . trans('Enabled') . '</span>' :
                                    '<span class="label-basic label-basic--danger">' . trans('Disabled') . '</span>';
                        })->editColumn('action', $this->getActionsColumn($canView, $canRun, $canUpdate))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Automation List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('Id')])
                        ->addColumn(['data' => 'title', 'name' => 'title', 'title' => trans('antares/automation::messages.datatable.headers.script_name'), 'className' => 'bolded'])
                        ->addColumn(['data' => 'category_id', 'name' => 'category_id', 'title' => trans('antares/automation::messages.datatable.headers.category')])
                        ->addColumn(['data' => 'active', 'name' => 'active', 'title' => trans('antares/automation::messages.datatable.headers.status')])
                        ->addColumn(['data' => 'description', 'name' => 'description', 'title' => trans('antares/automation::messages.datatable.headers.description'), 'orderable' => false])
                        ->addColumn(['data' => 'interval', 'name' => 'active', 'title' => trans('antares/automation::messages.datatable.headers.interval')])
                        ->addColumn(['data' => 'last_run', 'name' => 'last_run', 'title' => trans('antares/automation::messages.datatable.headers.last_run')])
                        ->addColumn(['data' => 'last_run_result', 'name' => 'last_run_result', 'title' => trans('antares/automation::messages.datatable.headers.last_run_result')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'mass-actions dt-actions', 'orderable' => false, 'searchable' => false])
                        ->setDeferedData()
                        ->addGroupSelect($this->categories(), 2, 'all', ['data-prefix' => trans('antares/automation::messages.datatable.select_category')]);
    }

    /**
     * Creates options for automation table categories
     * 
     * @return Collection
     */
    protected function categories(): \Illuminate\Support\Collection
    {

        $options = JobsCategory::all(['id', 'title'])->pluck('title', 'id');
        return $options->prepend(trans('antares/automation::messages.datatable.select_all'), 'all');
    }

    /**
     * Finds custom option id
     * 
     * @return mixes
     */
    protected function findCustomOptionId()
    {
        return JobsCategory::where('name', 'custom')->first()->id;
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
                $btns[] = $html->create('li', $html->link(handles("antares::automation/show/" . $row->id), trans('antares/automation::messages.show_logs'), ['data-icon' => 'desktop-windows']));
            }
            if ($canRun) {
                $btns[] = $html->create('li', $html->link(handles("antares::automation/run/" . $row->id, ['csrf' => true]), trans('Run'), ['class' => "triggerable confirm", 'data-icon' => 'play-circle-outline', 'data-title' => trans('antares/automation::messages.ask'), 'data-description' => trans('antares/automation::messages.running_job_message', ['name' => $row->value['title']])]));
            }
            if ($canUpdate) {
                $btns[] = $html->create('li', $html->link(handles("antares::automation/edit/" . $row->id), trans('antares/automation::messages.edit'), ['data-icon' => 'edit']));
            }
            if (empty($btns)) {
                return '';
            }
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}
